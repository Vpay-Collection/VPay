<?php
/*
 * Copyright (c) 2023. Ankio.  由CleanPHP4强力驱动。
 */

namespace cleanphp\closure\Contracts;

use Closure;

interface Serializable
{
    /**
     * Resolve the closure with the given arguments.
     *
     * @return mixed
     */
    public function __invoke();

    /**
     * Gets the closure that got serialized/unserialized.
     *
     * @return Closure
     */
    public function getClosure();
}
