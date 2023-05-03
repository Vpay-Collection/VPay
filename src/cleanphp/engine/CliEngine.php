<?php
/*******************************************************************************
 * Copyright (c) 2022. Ankio. All Rights Reserved.
 ******************************************************************************/

/**
 * Package: cleanphp\engine
 * Class CliEngine
 * Created By ankio.
 * Date : 2023/4/25
 * Time : 16:17
 * Description : 命令行工作引擎
 */

namespace cleanphp\engine;

use cleanphp\base\Response;

class CliEngine extends BaseEngine
{

    /**
     * @inheritDoc
     */
    function getContentType(): string
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    function render(...$data): string
    {
        return print_r($data, true);
    }

    /**
     * @inheritDoc
     */
    function renderError(string $msg, array $traces, string $dumps, string $tag)
    {
        return print_r([$msg, $traces, $dumps], true);
    }

    function onNotFound($msg = "")
    {
        (new Response())->render($msg)->send();
    }
}