<?php
/*
 * Copyright (c) 2023. Ankio.  由CleanPHP4强力驱动。
 */

namespace cleanphp\closure;

/**
 * Closure context class
 * @internal
 */
class ClosureContext
{
    public ClosureScope $scope;

    public int $locks;

    public function __construct()
    {
        $this->scope = new ClosureScope();
        $this->locks = 0;
    }
}