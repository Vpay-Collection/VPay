<?php
/*******************************************************************************
 * Copyright (c) 2022. Ankio. All Rights Reserved.
 ******************************************************************************/

/**
 * Package: app\task
 * Class MailTasker
 * Created By ankio.
 * Date : 2023/5/15
 * Time : 15:28
 * Description :
 */

namespace app\task;

use Throwable;

class MailTasker extends \library\task\TaskerAbstract
{

    /**
     * @inheritDoc
     */
    public function getTimeOut(): int
    {
        // TODO: Implement getTimeOut() method.
    }

    /**
     * @inheritDoc
     */
    public function onStart()
    {
        // TODO: Implement onStart() method.
    }

    /**
     * @inheritDoc
     */
    public function onStop()
    {
        // TODO: Implement onStop() method.
    }

    /**
     * @inheritDoc
     */
    public function onAbort(Throwable $e)
    {
        // TODO: Implement onAbort() method.
    }
}