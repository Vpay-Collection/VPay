<?php
/**
 * environmental check
 */

class check{
    private $conf;
    public function __construct()
    {
        $this->conf = include_once dirname(__FILE__)."/var.php";
    }

    public function env()
    {
        $env_items[] = array('name' => '操作系统', 'min' => '无限制', 'good' => 'linux', 'cur' => PHP_OS, 'status' => 1);

        $env_items[] = array('name' => 'PHP版本', 'min' => '7.0', 'good' => '7.3', 'cur' => PHP_VERSION, 'status' => (PHP_VERSION < 7.0 ? 0 : 1));

        $env_items[] = array('name' => '附件上传', 'min' => '未限制', 'good' => '2M', 'cur' => ini_get('upload_max_filesize'), 'status' => 1);

        $disk_place = function_exists('disk_free_space') ? floor(disk_free_space(ROOT_PATH) / (1024 * 1024)) : 0;
        $env_items[] = array('name' => '磁盘空间', 'min' => '100M', 'good' => '>100M', 'cur' => empty($disk_place) ? '未知' : $disk_place . 'M', 'status' => $disk_place < 100 ? 0 : 1);

        return $env_items;
    }

    /**
     * file check
     */
    public function dirfile()
    {

        foreach ($this->conf["dirfile"] as $key => $item) {
            $item_path = '/' . $item['path'];
            $dirfile_items[$key]["path"]=$item['path'];
            if ($item['type'] === 'dir') {
                if (!$this->dir_writeable(APP_PATH . $item_path)) {
                    if (is_dir(APP_PATH . $item_path)) {
                        $dirfile_items[$key]['status'] = 0;
                        $dirfile_items[$key]['current'] = '+r';
                    } else {
                        $dirfile_items[$key]['status'] = -1;
                        $dirfile_items[$key]['current'] = 'nodir';
                    }
                } else {
                    $dirfile_items[$key]['status'] = 1;
                    $dirfile_items[$key]['current'] = '+r+w';
                }
            } else {
                if (file_exists(APP_PATH . $item_path)) {
                    if (is_writable(APP_PATH . $item_path)) {

                        $dirfile_items[$key]['status'] = 1;
                        $dirfile_items[$key]['current'] = '+r+w';
                    } else {
                        $dirfile_items[$key]['status'] = 0;
                        $dirfile_items[$key]['current'] = '+r';
                    }
                } else {
                    if ($fp = @fopen(APP_PATH . $item_path, 'wb+')) {
                        $dirfile_items[$key]['status'] = 1;
                        $dirfile_items[$key]['current'] = '+r+w';
                        @fclose($fp);
                        @unlink(APP_PATH . $item_path);
                    } else {
                        $dirfile_items[$key]['status'] = -1;
                        $dirfile_items[$key]['current'] = 'nofile';
                    }
                }
            }
        }
        return $dirfile_items;
    }

    /**
     * dir is writeable
     * @return number
     */
    private function dir_writeable($dir)
    {
        $writeable = 0;
        if (!is_dir($dir)) {
            @mkdir($dir, 0755);
        } else {
            @chmod($dir, 0755);
        }
        if (is_dir($dir)) {
            if ($fp = @fopen("$dir/test.txt", 'w')) {
                @fclose($fp);
                @unlink("$dir/test.txt");
                $writeable = 1;
            } else {
                $writeable = 0;
            }
        }
        return $writeable;
    }

    /**
     * function is exist
     */
    public function func()
    {

        foreach ($this->conf["func"] as $key => $item) {
            $func_items[$key]["name"]=$item['name'];
            $func_items[$key]['status'] = function_exists($item['name']) ? 1 : 0;
        }
        return $func_items;
    }

    public function ext()
    {

        foreach ($this->conf["ext"] as $key => $item) {
            $ext_items[$key]["name"]=$item['name'];
            $ext_items[$key]['status'] = extension_loaded($item['name']) ? 1 : 0;
        }
        return $ext_items;
    }

}
class mysql{
    private $install_error;
    public function install()
    {
        if ($_POST['submitform'] != 'submit') return $this->install_error;

        $db_host = $_POST['db_host'];
        $db_port = $_POST['db_port'];
        $db_user = $_POST['db_user'];
        $db_pwd = $_POST['db_pwd'];
        $db_name = $_POST['db_name'];
        $admin = $_POST['admin'];
        $password = $_POST['password'];
        if (!$db_host || !$db_port || !$db_user || !$db_pwd || !$db_name || !$admin || !$password) {
            $this->install_error = '输入不完整，请检查';
            return false;
        }

        if (strlen($admin) > 15 || preg_match("/^$|^c:\\con\\con$|　|[,\"\s\t\<\>&]|^游客|^Guest/is", $admin)) {
            $this->install_error .= '非法用户名，用户名长度不应当超过 15 个英文字符，且不能包含特殊字符，一般是中文，字母或者数字';
            return false;
        }

        $mysqli = @ new mysqli($db_host, $db_user, $db_pwd, '', $db_port);
        if ($mysqli->connect_error) {
            $this->install_error = '数据库连接失败';
            return false;
        }

        if ($mysqli->get_server_info() > '5.0') {
            $mysqli->query("CREATE DATABASE IF NOT EXISTS `$db_name` DEFAULT CHARACTER SET " . DBCHARSET);
        } else {
            $this->install_error = '数据库必须为MySQL5.0版本以上';
            return false;
        }
        if ($mysqli->error) {
            $this->install_error = $mysqli->error;
            return false;
        }


        require(dirname(dirname(__FILE__))."/views/step_4.html");


        $this->write_config();

        $_charset = strtolower(DBCHARSET);
        $mysqli->select_db($db_name);
        $mysqli->set_charset($_charset);
        $sql = file_get_contents(dirname(dirname(__FILE__))."/data/mysql.sql");
        //判断是否安装测试数据
        $sql = str_replace("\r\n", "\n", $sql);
        $this->runquery($sql, $mysqli, $admin, $password);
        $this->showjsmessage('初始化数据 ... 成功 ');

        /**
         * 转码
         */
        $sitename = $_POST['site_name'];
        $username = $_POST['admin'];
        $password = $_POST['password'];


        exit("<script type=\"text/javascript\">document.getElementById('install_process').innerHTML = '安装完成，下一步...';document.getElementById('install_process').href='?step=5&sitename={$sitename}&username={$username}&password={$password}';</script>");

    }
    public function getErr(){
        return $this->install_error;
    }
//execute sql
    function runquery($sql, $mysqli, $admin, $password)
    {
//  global $lang, $tablepre, $db;
        if (!isset($sql) || empty($sql)) return;
        // $sql = str_replace("\r", "\n", str_replace('#__', $db_prefix, $sql));
        $sql = str_replace("[user]", $admin, $sql);
        //
        $sql = str_replace("[pass]", hash("sha256",md5($password.md5($admin))), $sql);
        $ret = array();
        $num = 0;
        foreach (explode(";\n", trim($sql)) as $q) {
            $ret[$num] = '';
            $queries = explode("\n", trim($q));
            foreach ($queries as $query) {
                $ret[$num] .= (isset($query[0]) && $query[0] == '#') || (isset($query[1]) && isset($query[1]) && $query[0] . $query[1] == '--') ? '' : $query;
            }
            $num++;
        }
        unset($sql);


        foreach ($ret as $query) {
            $query = trim($query);
            if ($query) {
                if (substr($query, 0, 12) == 'CREATE TABLE') {
                    $line = explode('`', $query);
                    $data_name = $line[1];
                    $this->showjsmessage('数据表  ' . $data_name . ' ... 创建成功');
                    $mysqli->query($this->droptable($data_name));
                    $mysqli->query($query);
                    unset($line, $data_name);
                } else {
                    $mysqli->query($query);
                }
            }
        }
    }

//抛出JS信息
    private function showjsmessage($message)
    {
        echo '<script type="text/javascript">showmessage(\'' . addslashes($message) . ' \');</script>' . "\r\n";
        flush();
        ob_flush();
    }

//写入config文件
    function write_config()
    {
        extract($GLOBALS, EXTR_SKIP);
        $config = ROOT_PATH.'/data/config.php';
        $configfile = @file_get_contents($config);
        $configfile = trim($configfile);
        $configfile = substr($configfile, -2) == '?>' ? substr($configfile, 0, -2) : $configfile;
        $charset = 'utf8';
        $db_host = $_POST['db_host'];
        $db_port = $_POST['db_port'];
        $db_user = $_POST['db_user'];
        $db_pwd = $_POST['db_pwd'];
        $db_name = $_POST['db_name'];
        $url = $_POST['url'];

        $configfile = str_replace("[url]", $url, $configfile);
        $configfile = str_replace("[charset]", $charset, $configfile);
        $configfile = str_replace("[host]", $db_host, $configfile);
        $configfile = str_replace("[user]", $db_user, $configfile);
        $configfile = str_replace("[pass]", $db_pwd, $configfile);
        $configfile = str_replace("[db]", $db_name, $configfile);
        $configfile = str_replace("[port]", $db_port, $configfile);
        @file_put_contents(APP_DIR.'/protected/config.php', $configfile);
    }
    private function droptable($table_name)
    {
        return "DROP TABLE IF EXISTS `" . $table_name . "`;";
    }
}