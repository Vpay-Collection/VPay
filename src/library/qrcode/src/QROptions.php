<?php
/**
 * Class QROptions
 *
 * @created      08.12.2015
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2015 Smiley
 * @license      MIT
 */

namespace library\qrcode\src;

use library\qrcode\src\Settings\SettingsContainerAbstract;

/**
 * The QRCode settings container
 */
class QROptions extends SettingsContainerAbstract
{
    use QROptionsTrait;
}
