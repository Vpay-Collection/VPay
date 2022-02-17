<?php


namespace app\lib\pay\lib;


use Exception;

/**
 * HttpClient
 *
 * Http client for HTTP protocol v 1.0 & 1.1
 * ---
 * @author   Ricardo Gamba <rgamba@gmail.com>
 * @license  GNU MIT
 * @version  1.0
 */
class HttpClient{
    const CRLF = "\r\n";
    private $request_std_headers=array(
        'Accept',
        'Accept-Charset',
        'Accept-Encoding',
        'Accept-Language',
        'Authorization',
        'Expect',
        'From',
        'Host',
        'If-Match',
        'If-Modified-Since',
        'If-None-Match',
        'If-Range',
        'If-Unmodified-Since',
        'Max-Forwards',
        'Proxy-Authorization',
        'Range',
        'Referer',
        'TE',
        'User-Agent'
    );
    private $response_std_headers=array(
        'Accept-Ranges',
        'Age',
        'Cache-Control',
        'Connection',
        'Date',
        'ETag',
        'Location',
        'Proxy-Authenticate',
        'Retry-After',
        'Server',
        'Transfer-Encoding',
        'Vary',
        'WWW-Authenticate'
    );
    private $entity_headers=array(
        'Allow',
        'Content-Encoding',
        'Content-Language',
        'Content-Length',
        'Content-Location',
        'Content-MD5',
        'Content-Range',
        'Content-Type',
        'Expires',
        'Last-Modified'
    );
    private $protocol_version="1.1";
    private $socket;
    private $method;
    private $status_code;
    private $status_msg;
    private $request_headers=array();
    private $response_headers=array();
    private $raw_request;
    private $raw_response;
    private $request_body;
    private $response_body;
    private $sys_err=array('no'=>NULL,'msg'=>NULL);
    private $follow_redirects=false;
    private $max_redirects=5;
    private $cur_redirects=0;
    private $auth_user;
    private $auth_pass;
    private $auth_login=false;
    private $auth_type="basic";
    private $auth_nonce_count=1;
    private $www_auth=array();
    private $cookies=array();
    private $accept_cookies=true;
    private $log="";

    public $scheme;
    public $url;
    public $uri;
    public $query;
    public $host;
    public $port=80;
    public $user_agent="Mozilla/5.0 (Macintosh; Intel Mac OS X 11_1_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.192 Safari/537.36";

    /**
     * Constructor
     *
     * @param mixed $url "http://www.example.com[:80][/path/[index.php[?var=val&var2=val2]]]"
     */
    public function __construct($url=NULL){
        if($url==NULL)
            return;
        $this->parseUrl($url);
    }

    /**
     * Add header to the request
     *
     * @param mixed $name
     * @param mixed $val
     */
    public function setHeader($name=NULL,$val=NULL){
        if(empty($name))
            return false;
        $this->request_headers[str_replace(' ','-',ucwords(str_replace('-',' ',$name)))]=$val;
        return true;
    }

    /**
     * Set the body of the request
     *
     * @param mixed $content
     */
    public function setBody($content=NULL){
        if($content==NULL)
            return false;
        $this->request_body=$content;
        return true;
    }

    /**
     * Send HTTP Head command
     *
     * @param mixed $uri
     * @param string $vars status number
     */
    public function head($uri=NULL,$vars=NULL){
        if(!empty($uri)) $this->uri=$uri;
        if(!empty($vars) && is_array($vars))
            $vars=http_build_query($vars);
        if(empty($this->query))
            $this->query=$vars;
        else
            if(!empty($vars))
                $this->query.="&".$vars;
        if(!empty($this->query))
            $this->uri.="?".$this->query;
        $this->method="HEAD";
        $this->request_body=NULL;
        $this->send();
    }

    /**
     * Send HTTP Delete command
     *
     * @param mixed $uri
     * @param string $vars
     * @return string Status number
     */
    public function delete($uri=NULL,$vars=NULL){
        if(!empty($uri)) $this->uri=$uri;
        if(!empty($vars) && is_array($vars))
            $vars=http_build_query($vars);
        if(empty($this->query))
            $this->query=$vars;
        else
            if(!empty($vars))
                $this->query.="&".$vars;
        if(!empty($this->query))
            $this->uri.="?".$this->query;
        $this->method="DELETE";
        $this->request_body=NULL;
        $this->send();
        return $this->status_code;
    }

    /**
     * Send HTTP Trace command
     *
     * @param mixed $uri
     * @param string $vars
     * @return string status number
     */
    public function trace($uri=NULL,$vars=NULL){
        if(!empty($uri)) $this->uri=$uri;
        if(!empty($vars) && is_array($vars))
            $vars=http_build_query($vars);
        if(empty($this->query))
            $this->query=$vars;
        else
            if(!empty($vars))
                $this->query.="&".$vars;
        if(!empty($this->query))
            $this->uri.="?".$this->query;
        $this->method="TRACE";
        $this->request_body=NULL;
        foreach($this->request_headers as $k => $v){
            if(in_array($k,$this->entity_headers))
                unset($this->request_headers[$k]);
        }
        $this->send();
        return $this->status_code;
    }

    /**
     * Execute HTTP Get command
     *
     * @param mixed $uri
     * @param string $vars
     * @return string status
     */
    public function get($uri=NULL,$vars=NULL){
        if(!empty($uri)) $this->uri=$uri;
        if(!empty($vars) && is_array($vars))
            $vars=http_build_query($vars);
        if(empty($this->query))
            $this->query=$vars;
        else
            if(!empty($vars))
                $this->query.="&".$vars;
        if(!empty($this->query))
            $this->uri.="?".$this->query;
        $this->method="GET";
        $this->request_body=NULL;
        $this->send();
        return $this->status_code;
    }

    /**
     * Send HTTP Post command
     *
     * @param mixed $uri
     * @param string $content
     * @return string status code
     */
    public function post($uri=NULL,$content=NULL,$files=NULL){
        if(!empty($uri)) $this->uri=$uri;
        if(is_array($content) && empty($files)){
            $content=http_build_query($content,"","&");
            $this->setHeader('Content-Type','application/x-www-form-urlencoded');
        }
        if(!empty($files) && is_array($files)){
            $boundary=uniqid('---------------------------');
            $this->setHeader('Content-Type','multipart/form-data; boundary='.$boundary);
            if(!empty($this->content) && is_array($this->content)){
                foreach($this->content as $k => $v){
                    $cont.=$boundary.self::CRLF.'Content-Disposition: form-data; name="'.$k.'"'.self::CRLF.self::CRLF.$v.self::CRLF;
                }
            }
            foreach($files as $i => $file){
                $cont.=$boundary.self::CRLF.'Content-Disposition: form-data; name="'.$file['name'].'"; filename="'.$file['filename'].'"'.self::CRLF;
                $cont.="Content-Type: ".(empty($file['content-type']) ? 'application/octet-stream' : $file['content-type']).self::CRLF.self::CRLF;
                $cont.=$file['data'].self::CRLF;
            }
            $content=$cont;
        }
        $this->request_body=$content;
        $this->method="POST";
        $this->send();
        return $this->status_code;
    }

    /**
     * Send HTTP Put command
     *
     * @param string $content
     * @return string
     */
    public function put($content=NULL){
        if(is_array($content)){
            $content=http_build_query($content);
            $this->setHeader('Content-Type','application/x-www-form-urlencoded');
        }
        $this->request_body=$content.self::CRLF.self::CRLF;
        $this->method="PUT";
        $this->send();
        return $this->status_code;
    }

    /**
     * Get a specific response header
     *
     * @param mixed $key
     * @return mixed
     */
    public function getHeader($key){
        return $this->response_headers[$key];
    }

    /**
     * Get the response body
     *
     */
    public function getBody(){
        return $this->response_body;
    }

    /**
     * Get the response headers, associative array
     *
     * @return array
     */
    public function getHeaders(){
        return $this->response_headers;
    }

    /**
     * Retrieve the HTTP raw request command
     *
     * @return string
     */
    public function rawRequest(){
        return $this->raw_request;
    }

    /**
     * Retrieve the HTTP raw response command
     *
     * @return string
     */
    public function rawResponse(){
        return $this->raw_response;
    }

    /**
     * Get the request status
     *
     * @return object ('number' => 'status number', 'msg' => 'status message')
     */
    public function status(){
        $obj = new \ArrayObject();
        $obj->number=$this->status_code;
        $obj->msg=$this->status_msg;
        return $obj;
    }

    public function statusOk(){
        return $this->status()->number=="200";
    }

    /**
     * Set wether the client should follow the server redirects or not
     *
     * @param boolean $b
     * @param integer $n max number of redirects allowed
     */
    public function followRedirects($b,$n=NULL){
        $this->follow_redirects=$b==true;
        if(!is_null($n) && is_numeric($n))
            $this->max_redirects=(integer)$n;
    }

    /**
     * Set the maximum number of redirects allowed
     *
     * @param integer $n
     */
    public function maxRedirects($n){
        $this->max_redirects=$n;
    }

    /**
     * Set credentials for BASIC authentication
     *
     * @param mixed $usr
     * @param mixed $pass
     */
    public function basicAuth($usr,$pass){
        $this->auth_type="basic";
        $this->auth_login=true;
        $this->auth_user=$usr;
        $this->auth_pass=$pass;
    }

    /**
     * Set credentials for DIGEST authentication
     *
     * @param mixed $usr
     * @param mixed $pass
     */
    public function digestAuth($usr,$pass=NULL){
        $this->auth_type="digest";
        $this->auth_login=true;
        $this->auth_user=$usr;
        $this->auth_pass=$pass;
    }

    /**
     * Close the socket connection
     *
     */
    public function disconnect(){
        @fclose($this->socket);
    }

    /**
     * Delete all cookies from specified host
     *
     * @param mixed $host
     */
    public function eraseCookies($host=NULL){
        if(empty($host))
            $host=$this->host;
        unset($this->cookies[$host]);
    }

    /**
     * Accept or deny cookies
     *
     * @param mixed $allow
     */
    public function acceptCookies($allow=true){
        $this->accept_cookies=($allow==true);
    }

    /**
     * Obtain the complete log of communication, requests,
     * responses and all redirects
     *
     */
    public function getLogHistory(){
        return $this->log;
    }

    private function parseUrl($url){
        $this->url=$url;
        $u=@parse_url($url);

        $this->scheme=isset($u['scheme'])?$u['scheme']:"";
        $this->host=isset($u['host'])?$u['host']:"";
        $this->uri=isset($u['path'])?$u['path']:"/";
        if(!empty($u['port']))
            $this->port=$u['port'];
        $this->query=isset($u['query'])?$u['query']:"";
    }

    private function connect(){
        if(empty($this->url)){
            throw new Exception("Empty URL");
        }

        $contextOptions = array(

            'ssl' => array(

                'verify_peer' => false,

                'verify_peer_name' => false

            )

        );


        $context = stream_context_create($contextOptions);

        $this->socket = stream_socket_client(($this->scheme=="https" ? 'ssl://' : '').$this->host.":".($this->scheme=="https" ? 443 : $this->port), $this->sys_err['no'], $this->sys_err['msg'], 10, STREAM_CLIENT_CONNECT, $context);

       // $this->socket=fsockopen(($this->scheme=="https" ? 'ssl://' : '').$this->host,($this->scheme=="https" ? 443 : $this->port),$this->sys_err['no'],$this->sys_err['msg'],10);

        if(!$this->socket){
            throw new Exception("Error while trying to connect to the host: ".$this->sys_err['msg']);

        }
        return true;
    }

    private function send(){
        $this->clearBuffer();
        $this->raw_request=$this->method." ".(empty($this->uri) ? "/" : $this->uri)." HTTP/".$this->protocol_version.self::CRLF;
        $this->raw_request.="Host: ".$this->host.self::CRLF;
        if(!empty($this->request_body))
            $this->raw_request.="Content-Length: ".strlen($this->request_body).self::CRLF;
        else
            unset($this->request_headers['Content-Length']);
        unset($this->request_headers['User-Agent']);
        $this->setHeader('User-Agent',$this->user_agent);
        if(empty($this->request_headers['Accept']))
            $this->setHeader('Accept','*/*');
        if($this->auth_login!=false){
            $this->setCredentials();
        }
        if(empty($this->request_headers['Connection']))
            $this->setHeader('Connection','close');
        $this->addCookieHeaders();
        if(!empty($this->request_headers)){
            foreach($this->request_headers as $name => $val)
                $this->raw_request.=$name.": ".$val.self::CRLF;
        }
        $this->raw_request.=self::CRLF;
        if(!empty($this->request_body))
            $this->raw_request.=$this->request_body.self::CRLF.self::CRLF;
        $this->logHistory($this->raw_request);
        if(!$this->socket){
            try{
                $this->connect();
            }catch(Exception $e){
                throw new Exception($e->getMessage());
            }
        }
        fwrite($this->socket,$this->raw_request);
        if(!$this->parseResponse())
            throw new Exception("Unknown response protocol");
    }

    private function setCredentials(){
        if($this->auth_type=="basic"){
            $this->setHeader('Authorization','Basic '.base64_encode($this->auth_user.":".$this->auth_pass));
        }else{
            if(!empty($this->www_auth)){
                $ha1=md5($this->auth_user.$this->www_auth['realm'].$this->auth_pass);
                $ha2=md5($this->method.":".$this->uri);
                $cnonce=uniqid('000');
                if($this->www_auth['qop']=="auth,auth-int" || $this->www_auth['qop']=="auth" || $this->www_auth['qop']=="auth-int"){
                    $response=md5($ha1.":".sprintf("%08d",$this->auth_nonce_count).":".$cnonce.":".$this->www_auth['qop'].":".$ha2);
                }else{
                    $response=md5($ha1.":".$this->www_auth['nonce'].$ha2);
                }
                $digest=array(
                    'username' => '"'.$this->auth_user.'"',
                    'realm' => '"'.$this->www_auth['realm'].'"',
                    'nonce' => '"'.$this->www_auth['nonce'].'"',
                    'uri' => '"'.$this->uri.'"',
                    'qop' => 'auth',
                    'nc' => sprintf("%08d",$this->auth_nonce_count),
                    'cnonce' => '"'.$cnonce.'"',
                    'response' => '"'.$response.'"',
                    'opaque' => '"'.$this->www_auth['opaque'].'"'
                );
                $digest="Digest ".implode(',',$digest);
                $this->setHeader('Authorization',$digest);
                $this->auth_nonce_count++;
            }
        }
    }

    private function parseResponse(){
        if(!$this->socket)
            return false;
        $in_head=true;
        $body="";
        $body_i=0;
        $i=0;
        $chunk_size=0;

        while(!feof($this->socket)){
            $line=@fgets($this->socket,4096);
            $this->raw_response.=$line;
            if(empty($line) || $line==self::CRLF){
                $in_head=false;
                continue;
            }
            if($i==0){
                if(!$this->parseStatus($line))
                    return false;
                $i++;
                continue;
            }
            $i++;
            if($in_head)
                $this->parseHeader($line);
            else{
              //  dump($this->response_headers,true);
                if(isset($this->response_headers['Transfer-Encoding'])&&strtolower($this->response_headers['Transfer-Encoding'])=="chunked"){
                    if(preg_match('/^[0-9a-f]+$/',trim($line))==1){
                        continue;
                    }
                }
                $body.=$line;
            }
        }
        $this->logHistory($this->raw_response);
        $this->response_body=$body;
        $this->processHeaders();
        return true;
    }

    private function processHeaders(){
        if(empty($this->response_headers['Location']))
            $this->cur_redirects=0;
        foreach($this->response_headers as $k => $v){
            switch($k){
                case 'Location':
                    if($this->follow_redirects && $this->cur_redirects<=$this->max_redirects){
                        $this->redirect($v);
                        $this->cur_redirects++;
                    }
                    break;
                case 'Connection':
                    if(strtolower($v)=="close")
                        $this->disconnect();
                    break;
                case 'WWW-Authenticate':
                    $this->www_auth=array();
                    $auth_resp=explode(' ',$v,2);
                    if($auth_resp[0]=="Digest"){
                        $tokens=explode(',',$auth_resp[1]);
                        foreach($tokens as $token){
                            $t=explode('=',$token);
                            $this->www_auth[$t[0]]=str_replace('"','',$t[1]);
                        }
                        if(!empty($this->auth_user))
                            $this->redirect($this->url);
                    }
                    break;
            }
        }
    }

    private function redirect($url){
        $this->clearBuffer();
        $this->disconnect();
        $this->parseUrl($url);
        $this->connect();
        try{
            $this->get();
        }catch(Exception $e){
            die($e->getMessage());
        }

    }

    private function parseStatus($line){
        if(preg_match('/^HTTP\/(.)+/',$line)<=0)
            return false;
        $line=explode(" ",$line);
        $this->status_code=trim($line[1]);
        $this->status_msg=trim($line[2]);
        return true;
    }

    private function parseHeader($line){
        if(empty($line)) return false;
        $line=explode(":",trim($line),2);
        $header=trim($line[0]);
        if(strtolower($header)=="set-cookie"){
            $this->saveCookie(trim($line[1]));
        }
        $this->response_headers[$header]=trim($line[1]);
        return true;
    }

    private function saveCookie($cookie){
        if(!$this->accept_cookies)
            return false;
        $cookie=explode(';',$cookie);
        $_cookie=array();
        foreach($cookie as $c){
            $c=trim($c);
            list($k,$v)=explode('=',$c);
            if(strtolower($k)=="secure" || strtolower($k)=="path" || strtolower($k)=="domain" || strtolower($k)=="expires"){
                $_cookie['info'][strtolower($k)]=$v;
            }else{
                $_cookie['data'][$k]=$v;
            }
        }
        $this->cookies[$this->host][]=$_cookie;
        return true;
    }

    private function addCookieHeaders(){
        if(empty($this->cookies[$this->host]))
            return false;
        $cookies=array();
        foreach($this->cookies[$this->host] as $cookie){
            if(isset($cookie['info']['secure']) && $this->scheme!="https")
                continue;
            // Path validation pending...
            // Expire validation pending...
            foreach($cookie['data'] as $k => $v)
                $cookies[]="$k=$v";
        }
        $this->setHeader('Cookie',implode('; ',$cookies));
        return true;
    }

    private function clearBuffer(){
        $this->status_code="";
        $this->status_msg="";
        $this->raw_request=NULL;
        $this->raw_response=NULL;
        $this->response_body=NULL;
        $this->response_headers=array();
    }

    private function logHistory($l){
        $l.="\n----------\n";
        $this->log.=$l;
    }
}