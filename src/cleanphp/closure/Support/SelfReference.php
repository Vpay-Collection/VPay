<?php
/*
 * Copyright (c) 2023. Ankio.  由CleanPHP4强力驱动。
 */

namespace cleanphp\closure\Support;

class SelfReference
{
    /**
     * The unique hash representing the object.
     *
     * @var string
     */
    public $hash;

    /**
     * Creates a new self reference instance.
     *
     * @param  string  $hash
     * @return void
     */
    public function __construct($hash)
    {
        $this->hash = $hash;
    }
}
