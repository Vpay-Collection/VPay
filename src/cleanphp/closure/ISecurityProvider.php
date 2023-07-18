<?php
/*
 * Copyright (c) 2023. Ankio.  由CleanPHP4强力驱动。
 */

namespace cleanphp\closure;

interface ISecurityProvider
{
    /**
     * Sign serialized closure
     * @param string $closure
     * @return array
     */
    public function sign(string $closure): array;

    /**
     * Verify signature
     * @param array $data
     * @return ?bool
     */
    public function verify(array $data): ?bool;
}