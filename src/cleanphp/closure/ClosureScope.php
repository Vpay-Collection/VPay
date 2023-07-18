<?php
/*
 * Copyright (c) 2023. Ankio.  由CleanPHP4强力驱动。
 */

namespace cleanphp\closure;

use SplObjectStorage;

/**
 * Closure scope class
 * @internal
 */
class ClosureScope extends SplObjectStorage
{

    public int $serializations = 0;

    public int $toserialize = 0;
}