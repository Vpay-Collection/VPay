<?php

namespace library\release\css;

class CompressCss
{
    static function compress($file)
    {
        /* remove comments */
        $buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', file_get_contents($file));
        /* remove tabs, spaces, newlines, etc. */
        $buffer = str_replace(array("
", "\r", "\n", "\t", '  ', '    ', '    '), '', $buffer);
        file_put_contents($file, $buffer);
    }

}