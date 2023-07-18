<?php
/*
 * Copyright (c) 2023. Ankio.  由CleanPHP4强力驱动。
 */

namespace library\qrcode\src\Data;

use library\qrcode\src\Common\Mode;

/**
 * abstract methods for the several data modes
 */
abstract class QRDataModeAbstract implements QRDataModeInterface
{

    /**
     * The data to write
     */
    protected string $data;

    /**
     * QRDataModeAbstract constructor.
     *
     * @throws QRCodeDataException
     */
    public function __construct(string $data)
    {
        $data = $this::convertEncoding($data);

        if (!$this::validateString($data)) {
            throw new QRCodeDataException('invalid data');
        }

        $this->data = $data;
    }

    /**
     * returns the character count of the $data string
     */
    protected function getCharCount(): int
    {
        return strlen($this->data);
    }

    /**
     * @inheritDoc
     */
    public static function convertEncoding(string $string): string
    {
        return $string;
    }

    /**
     * shortcut
     */
    protected static function getLengthBits(int $versionNumber): int
    {
        return Mode::getLengthBitsForVersion(static::DATAMODE, $versionNumber);
    }

}
