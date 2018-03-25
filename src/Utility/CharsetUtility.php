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

namespace Sjorek\RuntimeCapability\Utility;

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
final class CharsetUtility
{
    /**
     * @return string[]
     */
    public static function getEncodings()
    {
        $encodings = mb_list_encodings();
        foreach (array_filter(array_map('mb_encoding_aliases', $encodings)) as $aliases) {
            $encodings = array_merge($encodings, $aliases);
        }

        return array_map('strtoupper', $encodings);
    }

    /**
     * On Windows the codepage 65001 refers to UTF-8.
     *
     * @var string
     *
     * @see https://docs.microsoft.com/en-us/cpp/c-runtime-library/locale-names-languages-and-country-region-strings
     * @see https://docs.microsoft.com/en-us/cpp/c-runtime-library/reference/setlocale-wsetlocale
     * @see https://docs.microsoft.com/en-us/cpp/c-runtime-library/code-pages
     * @see https://msdn.microsoft.com/library/windows/desktop/dd317756.aspx
     */
    const WINDOWS_CODEPAGE_UTF8 = '65001';

    /**
     * List of known Windows code-pages.
     *
     * Scrape via:
     * <pre>
     * $('td[data-th="Identifier"]').each(function(){console.log("'" + $(this).text() + "',");});
     * </pre>
     *
     * @var string[]
     *
     * @see https://msdn.microsoft.com/de-de/library/windows/desktop/dd317756(v=vs.85).aspx
     */
    const WINDOWS_CODEPAGES = [
        '037',
        '437',
        '500',
        '708',
        '709',
        '710',
        '720',
        '737',
        '775',
        '850',
        '852',
        '855',
        '857',
        '858',
        '860',
        '861',
        '862',
        '863',
        '864',
        '865',
        '866',
        '869',
        '870',
        '874',
        '875',
        '932',
        '936',
        '949',
        '950',
        '1026',
        '1047',
        '1140',
        '1141',
        '1142',
        '1143',
        '1144',
        '1145',
        '1146',
        '1147',
        '1148',
        '1149',
        '1200',
        '1201',
        '1250',
        '1251',
        '1252',
        '1253',
        '1254',
        '1255',
        '1256',
        '1257',
        '1258',
        '1361',
        '10000',
        '10001',
        '10002',
        '10003',
        '10004',
        '10005',
        '10006',
        '10007',
        '10008',
        '10010',
        '10017',
        '10021',
        '10029',
        '10079',
        '10081',
        '10082',
        '12000',
        '12001',
        '20000',
        '20001',
        '20002',
        '20003',
        '20004',
        '20005',
        '20105',
        '20106',
        '20107',
        '20108',
        '20127',
        '20261',
        '20269',
        '20273',
        '20277',
        '20278',
        '20280',
        '20284',
        '20285',
        '20290',
        '20297',
        '20420',
        '20423',
        '20424',
        '20833',
        '20838',
        '20866',
        '20871',
        '20880',
        '20905',
        '20924',
        '20932',
        '20936',
        '20949',
        '21025',
        '21027',
        '21866',
        '28591',
        '28592',
        '28593',
        '28594',
        '28595',
        '28596',
        '28597',
        '28598',
        '28599',
        '28603',
        '28605',
        '29001',
        '38598',
        '50220',
        '50221',
        '50222',
        '50225',
        '50227',
        '50229',
        '50930',
        '50931',
        '50933',
        '50935',
        '50936',
        '50937',
        '50939',
        '51932',
        '51936',
        '51949',
        '51950',
        '52936',
        '54936',
        '57002',
        '57003',
        '57004',
        '57005',
        '57006',
        '57007',
        '57008',
        '57009',
        '57010',
        '57011',
        '65000',
        '65001',
    ];
}
