<?php
/*
 * Copyright (c) 2023. Ankio. All Rights Reserved.
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