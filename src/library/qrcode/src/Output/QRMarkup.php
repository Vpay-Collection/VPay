<?php
/*
 * Copyright (c) 2023. Ankio.  由CleanPHP4强力驱动。
 */

namespace library\qrcode\src\Output;

use function is_string;
use function preg_match;
use function strip_tags;
use function trim;

/**
 * Abstract for markup types: HTML, SVG, ... XML anyone?
 */
abstract class QRMarkup extends QROutputAbstract
{

    /**
     * note: we're not necessarily validating the several values, just checking the general syntax
     * note: css4 colors are not included
     *
     * @todo: XSS proof
     *
     * @see https://developer.mozilla.org/en-US/docs/Web/CSS/color_value
     * @inheritDoc
     */
    public static function moduleValueIsValid($value): bool
    {

        if (!is_string($value)) {
            return false;
        }

        $value = trim(strip_tags($value), " '\"\r\n\t");

        // hex notation
        // #rgb(a)
        // #rrggbb(aa)
        if (preg_match('/^#([\da-f]{3}){1,2}$|^#([\da-f]{4}){1,2}$/i', $value)) {
            return true;
        }

        // css: hsla/rgba(...values)
        if (preg_match('#^(hsla?|rgba?)\([\d .,%/]+\)$#i', $value)) {
            return true;
        }

        // predefined css color
        if (preg_match('/^[a-z]+$/i', $value)) {
            return true;
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    protected function prepareModuleValue($value): string
    {
        return trim(strip_tags($value), " '\"\r\n\t");
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultModuleValue(bool $isDark): string
    {
        return ($isDark) ? $this->options->markupDark : $this->options->markupLight;
    }

    /**
     * @inheritDoc
     */
    public function dump(string $file = null): string
    {
        $data = $this->createMarkup($file !== null);

        $this->saveToFile($data, $file);

        return $data;
    }

    /**
     * returns a string with all css classes for the current element
     */
    abstract protected function getCssClass(int $M_TYPE): string;

    /**
     *
     */
    abstract protected function createMarkup(bool $saveToFile): string;
}
