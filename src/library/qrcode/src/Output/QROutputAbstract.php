<?php
/*
 * Copyright (c) 2023. Ankio.  由CleanPHP4强力驱动。
 */

namespace library\qrcode\src\Output;

use Closure;
use library\qrcode\src\Data\QRMatrix;
use library\qrcode\src\QROptions;
use library\qrcode\src\Settings\SettingsContainerInterface;
use function base64_encode;
use function dirname;
use function file_put_contents;
use function is_writable;
use function ksort;
use function sprintf;

/**
 * common output abstract
 */
abstract class QROutputAbstract implements QROutputInterface
{

    /**
     * the current size of the QR matrix
     *
     * @see QRMatrix::getSize
     */
    protected int $moduleCount;

    /**
     * the current scaling for a QR pixel
     *
     * @see \library\qrcode\src\QROptions::$scale
     */
    protected int $scale;

    /**
     * the side length of the QR image (modules * scale)
     */
    protected int $length;

    /**
     * an (optional) array of color values for the several QR matrix parts
     */
    protected array $moduleValues;

    /**
     * the (filled) data matrix object
     */
    protected QRMatrix $matrix;

    /**
     * @var SettingsContainerInterface|QROptions
     */
    protected SettingsContainerInterface $options;

    /**
     * QROutputAbstract constructor.
     */
    public function __construct(SettingsContainerInterface $options, QRMatrix $matrix)
    {
        $this->options = $options;
        $this->matrix = $matrix;

        $this->setMatrixDimensions();
        $this->setModuleValues();
    }

    /**
     * Sets/updates the matrix dimensions
     *
     * Call this method if you modify the matrix from within your custom module in case the dimensions have been changed
     */
    protected function setMatrixDimensions(): void
    {
        $this->moduleCount = $this->matrix->getSize();
        $this->scale = $this->options->scale;
        $this->length = ($this->moduleCount * $this->scale);
    }

    /**
     * Sets the initial module values
     */
    protected function setModuleValues(): void
    {

        foreach ($this::DEFAULT_MODULE_VALUES as $M_TYPE => $defaultValue) {
            $value = ($this->options->moduleValues[$M_TYPE] ?? null);

            $this->moduleValues[$M_TYPE] = $this::moduleValueIsValid($value)
                ? $this->prepareModuleValue($value)
                : $this->getDefaultModuleValue($defaultValue);
        }

    }

    /**
     * Returns the prepared value for the given $M_TYPE
     *
     * @return mixed|null return value depends on the output class
     */
    protected function getModuleValue(int $M_TYPE)
    {
        return ($this->moduleValues[$M_TYPE] ?? null);
    }

    /**
     * Returns the prepared module value at the given coordinate [$x, $y] (convenience)
     *
     * @return mixed|null
     */
    protected function getModuleValueAt(int $x, int $y)
    {
        return $this->getModuleValue($this->matrix->get($x, $y));
    }

    /**
     * Prepares the value for the given input ()
     *
     * @param mixed $value
     *
     * @return mixed|null return value depends on the output class
     */
    abstract protected function prepareModuleValue($value);

    /**
     * Returns a default value for either dark or light modules
     *
     * @return mixed|null return value depends on the output class
     */
    abstract protected function getDefaultModuleValue(bool $isDark);

    /**
     * Returns a base64 data URI for the given string and mime type
     */
    protected function toBase64DataURI(string $data, string $mime): string
    {
        return sprintf('data:%s;base64,%s', $mime, base64_encode($data));
    }

    /**
     * Saves the qr $data to a $file. If $file is null, nothing happens.
     *
     * @throws QRCodeOutputException
     * @see \library\qrcode\src\QROptions::$cachefile
     *
     * @see file_put_contents()
     */
    protected function saveToFile(string $data, string $file = null): void
    {

        if ($file === null) {
            return;
        }

        if (!is_writable(dirname($file))) {
            throw new QRCodeOutputException(sprintf('Cannot write data to cache file: %s', $file));
        }

        if (file_put_contents($file, $data) === false) {
            throw new QRCodeOutputException(sprintf('Cannot write data to cache file: %s (file_put_contents error)', $file));
        }
    }

    /**
     * collects the modules per QRMatrix::M_* type and runs a $transform function on each module and
     * returns an array with the transformed modules
     *
     * The transform callback is called with the following parameters:
     *
     *   $x            - current column
     *   $y            - current row
     *   $M_TYPE       - field value
     *   $M_TYPE_LAYER - (possibly modified) field value that acts as layer id
     */
    protected function collectModules(Closure $transform): array
    {
        $paths = [];

        // collect the modules for each type
        for ($y = 0; $y < $this->moduleCount; $y++) {
            for ($x = 0; $x < $this->moduleCount; $x++) {
                $M_TYPE = $this->matrix->get($x, $y);
                $M_TYPE_LAYER = $M_TYPE;

                if ($this->options->connectPaths && !$this->matrix->checkTypeIn($x, $y, $this->options->excludeFromConnect)) {
                    // to connect paths we'll redeclare the $M_TYPE_LAYER to data only
                    $M_TYPE_LAYER = QRMatrix::M_DATA;

                    if ($this->matrix->check($x, $y)) {
                        $M_TYPE_LAYER |= QRMatrix::IS_DARK;
                    }
                }

                // collect the modules per $M_TYPE
                $module = $transform($x, $y, $M_TYPE, $M_TYPE_LAYER);

                if (!empty($module)) {
                    $paths[$M_TYPE_LAYER][] = $module;
                }
            }
        }

        // beautify output
        ksort($paths);

        return $paths;
    }

}
