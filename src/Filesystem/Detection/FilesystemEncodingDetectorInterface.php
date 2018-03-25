<?php

declare(strict_types=1);

/*
 * This file is part of the Runtime Capability project.
 *
 * (c) Stephan Jorek <stephan.jorek@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sjorek\RuntimeCapability\Filesystem\Detection;

use Sjorek\RuntimeCapability\Detection\DetectorInterface;
use Sjorek\UnicodeNormalization\Implementation\NormalizationForms;

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
interface FilesystemEncodingDetectorInterface extends DetectorInterface
{
    /**
     * We use a pattern to identify the test files that have been created.
     *
     * @var string
     */
    const DETECTION_FILENAME_PATTERN = '.filesystem-encoding-test-%s-%s.txt';

    /**
     * We should use a sub-folder, as the operating might alter the given filenames.
     * A sub-folder is the only guaranteed chance to cleanup after detection.
     *
     * @var string
     */
    const DETECTION_FOLDER_NAME = '.filesystem-encoding-detection';

    /**
     * List of mapping unicode-normalization constants to filenames in corresponding unicode-normalizations.
     *
     * @var bool[]|string[]
     */
    const UTF8_FILENAME_TESTS = [
        // Raw binary data (not normalized, and not even a mix different normalizations):
        //
        //  php > $fileName = "ÖéöĄĆŻĘĆćążęóΘЩשݐซဤ⒜あ겫你你♥︎☺︎.txt";
        //  php > echo bin2hex($fileName);
        //  php > echo bin2hex(
        //         "\u{00D6}" // Ö
        //       . "\u{00E9}" // é - reserved character in Apple™'s HFS+ (OS X Extended) filesystem
        //       . "\u{00F6}" // ö
        //       . "\u{0104}" // Ą
        //       . "\u{0106}" // Ć
        //       . "\u{017B}" // Ż
        //       . "\u{0118}" // Ę
        //       . "\u{0106}" // Ć
        //       . "\u{0107}" // ć
        //       . "\u{0105}" // ą
        //       . "\u{017C}" // ż
        //       . "\u{0119}" // ę
        //       . "\u{00F3}" // ó
        //       . "\u{0398}" // Θ
        //       . "\u{0429}" // Щ
        //       . "\u{05E9}" // ש
        //       . "\u{0750}" // ݐ
        //       . "\u{0E0B}" // ซ︎
        //       . "\u{1024}" // ဤ
        //       . "\u{249C}" // ⒜  - special treatment in Apple™'s filename NFD normalization
        //       . "\u{3042}" // あ
        //       . "\u{ACAB}" // 겫
        //       . "\u{4F60}" // 你 - same as below, but in NFC
        //       . "\u{2F804}" // 你 - neither C, D, KC or KD + special in Apple™'s filename NFD normalization
        //       . "\u{2665}\u{FE0E}" // ♥
        //       . "\u{263A}\u{FE0E}" // ☺
        //       . ".txt"
        //  );
        // Many zeros to align with stuff below … turns into a single 0
        000000000000000000000000 => 'c396c3a9c3b6c484c486c5bbc498c486c487c485c5bcc499c3b3ce98d0a9d7a9dd90e0b88be180a4e2929ce38182eab2abe4bda0f0afa084e299a5efb88ee298baefb88e2e747874',

        // not normalized $fileName from above partially in NFC, partially in NFD and with special treatments
        // honestly, this filename is completely broken, so maybe this delivers some unexpected results
        //
        //  php > echo bin2hex(mb_substr($fileName, 0, 4) .
        //                     Normalizer::normalize(mb_substr($fileName, 4, 4), Normalizer::NFC).
        //                     Normalizer::normalize(mb_substr($fileName, 8, 4), Normalizer::NFD).
        //                     mb_substr($fileName, 12));
        //
        NormalizationForms::NONE => 'c396c3a9c3b6c484c486c5bbc498c48663cc8161cca87acc8765cca8c3b3ce98d0a9d7a9dd90e0b88be180a4e2929ce38182eab2abe4bda0f0afa084e299a5efb88ee298baefb88e2e747874',

        // NFC-normalized variant of $fileName from above
        //  php > echo bin2hex(Normalizer::normalize($fileName, Normalizer::NFC));
        NormalizationForms::NFC => 'c396c3a9c3b6c484c486c5bbc498c486c487c485c5bcc499c3b3ce98d0a9d7a9dd90e0b88be180a4e2929ce38182eab2abe4bda0e4bda0e299a5efb88ee298baefb88e2e747874',

        // NFD-normalized variant of $fileName from above
        //  php > echo bin2hex(Normalizer::normalize($fileName, Normalizer::NFD));
        NormalizationForms::NFD => '4fcc8865cc816fcc8841cca843cc815acc8745cca843cc8163cc8161cca87acc8765cca86fcc81ce98d0a9d7a9dd90e0b88be180a4e2929ce38182e18480e185a7e186aae4bda0e4bda0e299a5efb88ee298baefb88e2e747874',
        // look right for difference to NFD_MAC =>                                                                                                                                 ^^^^^^

        // NFD_MAC-normalized variant of $fileName from above, differing from NFD in 3 bytes
        //  php > echo bin2hex(iconv('utf-8', 'utf-8-mac', $fileName));
        NormalizationForms::NFD_MAC => '4fcc8865cc816fcc8841cca843cc815acc8745cca843cc8163cc8161cca87acc8765cca86fcc81ce98d0a9d7a9dd90e0b88be180a4e2929ce38182e18480e185a7e186aae4bda0efbfbde299a5efb88ee298baefb88e2e747874',
        // look right for difference to plain NFD =>                                                                                                                                   ^^^^^^

        // Not supported for file names, as those forms are lossy and inappropriate for filesystem-paths
        NormalizationForms::NFKD => false,
        NormalizationForms::NFKC => false,
    ];
}
