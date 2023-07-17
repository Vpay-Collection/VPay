<?php
/**
 * Class GDLuminanceSource
 *
 * @created      17.01.2021
 * @author       Ashot Khanamiryan
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2021 Smiley
 * @license      MIT
 *
 * @noinspection PhpComposerExtensionStubsInspection
 */

namespace library\qrcode\src\Decoder;

use GdImage;
use library\qrcode\src\Settings\SettingsContainerInterface;
use function file_get_contents;
use function get_resource_type;
use function imagecolorat;
use function imagecolorsforindex;
use function imagecreatefromstring;
use function imagefilter;
use function imagesx;
use function imagesy;
use function is_resource;
use const IMG_FILTER_BRIGHTNESS;
use const IMG_FILTER_CONTRAST;
use const IMG_FILTER_GRAYSCALE;
use const PHP_MAJOR_VERSION;

/**
 * This class is used to help decode images from files which arrive as GD Resource
 * It does not support rotation.
 */
class GDLuminanceSource extends LuminanceSourceAbstract
{

    /**
     * @var resource|GdImage
     */
    protected $gdImage;

    /**
     * GDLuminanceSource constructor.
     *
     * @param resource|GdImage $gdImage
     * @param SettingsContainerInterface|null $options
     *
     * @throws QRCodeDecoderException
     */
    public function __construct($gdImage, SettingsContainerInterface $options = null)
    {

        /** @noinspection PhpFullyQualifiedNameUsageInspection */
        if (
            (PHP_MAJOR_VERSION >= 8 && !$gdImage instanceof GdImage)
            || (PHP_MAJOR_VERSION < 8 && (!is_resource($gdImage) || get_resource_type($gdImage) !== 'gd'))
        ) {
            throw new QRCodeDecoderException('Invalid GD image source.'); // @codeCoverageIgnore
        }

        parent::__construct(imagesx($gdImage), imagesy($gdImage), $options);

        $this->gdImage = $gdImage;

        $this->setLuminancePixels();
    }

    /**
     *
     */
    protected function setLuminancePixels(): void
    {

        if ($this->options->readerGrayscale) {
            imagefilter($this->gdImage, IMG_FILTER_GRAYSCALE);
        }

        if ($this->options->readerIncreaseContrast) {
            imagefilter($this->gdImage, IMG_FILTER_BRIGHTNESS, -100);
            imagefilter($this->gdImage, IMG_FILTER_CONTRAST, -100);
        }

        for ($j = 0; $j < $this->height; $j++) {
            for ($i = 0; $i < $this->width; $i++) {
                $argb = imagecolorat($this->gdImage, $i, $j);
                $pixel = imagecolorsforindex($this->gdImage, $argb);

                $this->setLuminancePixel($pixel['red'], $pixel['green'], $pixel['blue']);
            }
        }

    }

    /** @inheritDoc */
    public static function fromFile(string $path, SettingsContainerInterface $options = null): self
    {
        return new self(imagecreatefromstring(file_get_contents(self::checkFile($path))), $options);
    }

    /** @inheritDoc */
    public static function fromBlob(string $blob, SettingsContainerInterface $options = null): self
    {
        return new self(imagecreatefromstring($blob), $options);
    }

}
