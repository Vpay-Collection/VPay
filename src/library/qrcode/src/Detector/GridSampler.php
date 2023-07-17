<?php
/**
 * Class GridSampler
 *
 * @created      17.01.2021
 * @author       ZXing Authors
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2021 Smiley
 * @license      Apache-2.0
 */

namespace library\qrcode\src\Detector;

use library\qrcode\src\Data\QRMatrix;
use library\qrcode\src\Decoder\BitMatrix;
use Throwable;
use function array_fill;
use function count;
use function sprintf;

/**
 * Implementations of this class can, given locations of finder patterns for a QR code in an
 * image, sample the right points in the image to reconstruct the QR code, accounting for
 * perspective distortion. It is abstracted since it is relatively expensive and should be allowed
 * to take advantage of platform-specific optimized implementations, like Sun's Java Advanced
 * Imaging library, but which may not be available in other environments such as J2ME, and vice
 * versa.
 *
 * The implementation used can be controlled by calling #setGridSampler(GridSampler)
 * with an instance of a class which implements this interface.
 *
 * @author Sean Owen
 */
final class GridSampler
{

    /**
     * Checks a set of points that have been transformed to sample points on an image against
     * the image's dimensions to see if the point are even within the image.
     *
     * This method will actually "nudge" the endpoints back onto the image if they are found to be
     * barely (less than 1 pixel) off the image. This accounts for imperfect detection of finder
     * patterns in an image where the QR Code runs all the way to the image border.
     *
     * For efficiency, the method will check points from either end of the line until one is found
     * to be within the image. Because the set of points are assumed to be linear, this is valid.
     *
     * @param BitMatrix $matrix image into which the points should map
     * @param float[] $points actual points in x1,y1,...,xn,yn form
     *
     * @throws QRCodeDetectorException if an endpoint is lies outside the image boundaries
     */
    private function checkAndNudgePoints(BitMatrix $matrix, array $points): void
    {
        $dimension = $matrix->getSize();
        $nudged = true;
        $max = count($points);

        // Check and nudge points from start until we see some that are OK:
        for ($offset = 0; $offset < $max && $nudged; $offset += 2) {
            $x = (int)$points[$offset];
            $y = (int)$points[($offset + 1)];

            if ($x < -1 || $x > $dimension || $y < -1 || $y > $dimension) {
                throw new QRCodeDetectorException(sprintf('checkAndNudgePoints 1, x: %s, y: %s, d: %s', $x, $y, $dimension));
            }

            $nudged = false;

            if ($x === -1) {
                $points[$offset] = 0.0;
                $nudged = true;
            } elseif ($x === $dimension) {
                $points[$offset] = ($dimension - 1);
                $nudged = true;
            }

            if ($y === -1) {
                $points[($offset + 1)] = 0.0;
                $nudged = true;
            } elseif ($y === $dimension) {
                $points[($offset + 1)] = ($dimension - 1);
                $nudged = true;
            }

        }
        // Check and nudge points from end:
        $nudged = true;
        $offset = (count($points) - 2);

        for (; $offset >= 0 && $nudged; $offset -= 2) {
            $x = (int)$points[$offset];
            $y = (int)$points[($offset + 1)];

            if ($x < -1 || $x > $dimension || $y < -1 || $y > $dimension) {
                throw new QRCodeDetectorException(sprintf('checkAndNudgePoints 2, x: %s, y: %s, d: %s', $x, $y, $dimension));
            }

            $nudged = false;

            if ($x === -1) {
                $points[$offset] = 0.0;
                $nudged = true;
            } elseif ($x === $dimension) {
                $points[$offset] = ($dimension - 1);
                $nudged = true;
            }

            if ($y === -1) {
                $points[($offset + 1)] = 0.0;
                $nudged = true;
            } elseif ($y === $dimension) {
                $points[($offset + 1)] = ($dimension - 1);
                $nudged = true;
            }

        }
    }

    /**
     * Samples an image for a rectangular matrix of bits of the given dimension. The sampling
     * transformation is determined by the coordinates of 4 points, in the original and transformed
     * image space.
     *
     * @return BitMatrix representing a grid of points sampled from the image within a region
     *   defined by the "from" parameters
     * @throws QRCodeDetectorException if image can't be sampled, for example, if the transformation defined
     *   by the given points is invalid or results in sampling outside the image boundaries
     */
    public function sampleGrid(BitMatrix $matrix, int $dimension, PerspectiveTransform $transform): BitMatrix
    {

        if ($dimension <= 0) {
            throw new QRCodeDetectorException('invalid matrix size');
        }

        $bits = new BitMatrix($dimension);
        $points = array_fill(0, (2 * $dimension), 0.0);

        for ($y = 0; $y < $dimension; $y++) {
            $max = count($points);
            $iValue = ($y + 0.5);

            for ($x = 0; $x < $max; $x += 2) {
                $points[$x] = (($x / 2) + 0.5);
                $points[($x + 1)] = $iValue;
            }

            $transform->transformPoints($points);
            // Quick check to see if points transformed to something inside the image;
            // sufficient to check the endpoints
            $this->checkAndNudgePoints($matrix, $points);

            try {
                for ($x = 0; $x < $max; $x += 2) {
                    // Black(-ish) pixel
                    $bits->set(($x / 2), $y, $matrix->check((int)$points[$x], (int)$points[($x + 1)]), QRMatrix::M_DATA);
                }
            } // @codeCoverageIgnoreStart
            catch (Throwable $aioobe) {//ArrayIndexOutOfBoundsException
                // This feels wrong, but, sometimes if the finder patterns are misidentified, the resulting
                // transform gets "twisted" such that it maps a straight line of points to a set of points
                // whose endpoints are in bounds, but others are not. There is probably some mathematical
                // way to detect this about the transformation that I don't know yet.
                // This results in an ugly runtime exception despite our clever checks above -- can't have
                // that. We could check each point's coordinates but that feels duplicative. We settle for
                // catching and wrapping ArrayIndexOutOfBoundsException.
                throw new QRCodeDetectorException('ArrayIndexOutOfBoundsException');
            }
            // @codeCoverageIgnoreEnd

        }

        return $bits;
    }

}
