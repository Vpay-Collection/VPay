<?php
/*******************************************************************************
 * Copyright (c) 2022. Ankio. All Rights Reserved.
 ******************************************************************************/

/**
 * Package: app\utils
 * Class GithubUpdater
 * Created By ankio.
 * Date : 2023/3/30
 * Time : 19:56
 * Description :
 */

namespace app\utils;

use cleanphp\base\Json;
use cleanphp\cache\Cache;
use cleanphp\objects\StringBuilder;
use library\http\HttpClient;
use library\http\HttpException;

class GithubUpdater
{
    private string $repo = "";

    /**
     * @param $repo string 仓库名，例如 dreamncn/pay
     */
    public function __construct(string $repo)
    {
        $this->repo = $repo;
    }

    /**
     * @param $repo string 仓库名，例如 dreamncn/pay
     * @return GithubUpdater
     */
    static function init(string $repo): GithubUpdater
    {
        return new GithubUpdater($repo);
    }

    /**
     * 检查版本
     * @param $version string 版本号
     * @param $new_version
     * @param $download_url
     * @return bool
     */
    public function check(string $version, &$new_version, &$download_url, &$body): bool
    {
        $new_version = $version;
        $download_url = "";
        try {
            $result = Cache::init(3600 * 24)->get($this->repo);
            $limit = Cache::init(3600)->get($this->repo . "_limit");
            //Github 接口速率限制
            if (empty($result) && empty($limit)) {
                $result = HttpClient::init("https://api.github.com")->get()->send("/repos/{$this->repo}/releases")->getBody();
                if (!(new StringBuilder($result))->contains("API rate limit exceeded")) {
                    //如果没有被限制，就缓存24小时
                    Cache::init(3600 * 24)->set($this->repo, $result);
                } else {
                    //如果被限制了就设置限制定时器，在1个小时后在试着去访问
                    Cache::init(3600)->set($this->repo . "_limit", true);
                }
            }
        } catch (HttpException $e) {
            return false;
        }
        if (empty($result)) return false;
        $release = Json::decode($result, true);

        if (!isset($release[0]['body'])) return false;
        $body = str_replace("\n", "<br>", (new Parsedown())->text($release[0]['body']));
        if (!isset($release[0]['tag_name'])) return false;
        // 获取最新版本号
        $new_version = $release[0]['tag_name'];

        if (!isset($release[0]['assets'][0]['browser_download_url'])) {
            $download_url = "https://github.com/repos/{$this->repo}/releases";
        } else {
            $download_url = $release[0]['assets'][0]['browser_download_url'];
        }

        // 比较版本号，确定是否需要更新
        if (version_compare($version, $new_version, '<')) {
            return true;
        }
        return false;
    }
}