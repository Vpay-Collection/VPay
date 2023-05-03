<?php
/*******************************************************************************
 * Copyright (c) 2022. Ankio. All Rights Reserved.
 ******************************************************************************/

/**
 * Package: app\exception
 * Class ChannelException
 * Created By ankio.
 * Date : 2023/3/13
 * Time : 14:53
 * Description :
 */

namespace app\exception;

class ChannelException extends \Exception
{
    private int $channel = 0;

    public function __construct($message = "", $channel = 0)
    {
        $this->channel = $channel;
        parent::__construct($message, 0, null);
    }

    /**
     * @return int
     */
    public function getChannel(): int
    {
        return $this->channel;
    }
}