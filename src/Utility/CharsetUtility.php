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
     * @param string $charset
     * @return string|null
     */
    public static function normalizeEncodingName(string $charset): ?string
    {
        $aliases = @mb_encoding_aliases(self::CODEPAGE_TO_CHARSET[$charset] ?? $charset);
        if (false === $aliases) {
            return null;
        }

        $aliases = array_filter($aliases);
        $charset = strtolower($charset);

        foreach(mb_list_encodings() as $encoding) {
            if ($charset === strtolower($encoding)) {
                return $encoding;
            }
            if (empty($aliases)) {
                continue;
            }
            if (empty(array_diff($aliases, array_filter(mb_encoding_aliases($encoding))))) {
                return $encoding;
            }
        }

        return null;
    }

    /**
     * @param string $locale
     * @return string|null
     */
    public static function getEncodingNameFromLocaleString(string $locale): ?string
    {
        if ('C' === $locale) {
            return 'ASCII';
        }

        $candidate = '';
        if (function_exists('nl_langinfo')) {
            $current = setlocale(LC_CTYPE, 0);
            if (false !== setlocale(LC_CTYPE, $locale)) {
                // returns '' if the codeset can not be determined
                $candidate = nl_langinfo(CODESET);
            }
            setlocale(LC_CTYPE, $current);
        }
        if ('' === $candidate) {
            $candidate = preg_replace('/^[^.]+\.([^@]+)(?:@.*)?$/', '$1', $locale);
        }

        return self::normalizeEncodingName($candidate);
    }

    /**
     * Map known codepages to charsets.
     *
     * scraped via:
     * <pre>
     * $('td[data-th='Identifier']').each(function(){console.log(''' + $(this).text() + '',');});
     * </pre>
     *
     * @var string[]
     * @see https://msdn.microsoft.com/de-de/library/windows/desktop/dd317756(v=vs.85).aspx
     */
    const CODEPAGE_TO_CHARSET = [
        '037' => 'IBM037',
        '437' => 'IBM437',
        '500' => 'IBM500',
        '708' => 'ASMO-708',
        '709' => '',
        '710' => '',
        '720' => 'DOS-720',
        '737' => 'IBM737',
        '775' => 'IBM775',
        '850' => 'CP850',
        '852' => 'IBM852',
        '855' => 'IBM855',
        '857' => 'IBM857',
        '858' => 'IBM00858',
        '860' => 'IBM860',
        '861' => 'IBM861',
        '862' => 'DOS-862',
        '863' => 'IBM863',
        '864' => 'IBM864',
        '865' => 'IBM865',
        '866' => 'CP866',
        '869' => 'IBM869',
        '870' => 'IBM870',
        '874' => 'WINDOWS-874',
        '875' => 'CP875',
        '932' => 'SJIS',
        '936' => 'EUC-CN',
        '949' => 'KS_C_5601-1987',
        '950' => 'BIG-5',
        '1026' => 'IBM1026',
        '1047' => 'IBM01047',
        '1140' => 'IBM01140',
        '1141' => 'IBM01141',
        '1142' => 'IBM01142',
        '1143' => 'IBM01143',
        '1144' => 'IBM01144',
        '1145' => 'IBM01145',
        '1146' => 'IBM01146',
        '1147' => 'IBM01147',
        '1148' => 'IBM01148',
        '1149' => 'IBM01149',
        '1200' => 'UTF-16',
        '1201' => 'UTF-16BE',
        '1250' => 'WINDOWS-1250',
        '1251' => 'Windows-1251',
        '1252' => 'Windows-1252',
        '1253' => 'WINDOWS-1253',
        '1254' => 'Windows-1254',
        '1255' => 'WINDOWS-1255',
        '1256' => 'WINDOWS-1256',
        '1257' => 'WINDOWS-1257',
        '1258' => 'WINDOWS-1258',
        '1361' => 'JOHAB',
        '10000' => 'MACINTOSH',
        '10001' => 'SJIS-mac',
        '10002' => 'X-MAC-CHINESETRAD',
        '10003' => 'X-MAC-KOREAN',
        '10004' => 'X-MAC-ARABIC',
        '10005' => 'X-MAC-HEBREW',
        '10006' => 'X-MAC-GREEK',
        '10007' => 'X-MAC-CYRILLIC',
        '10008' => 'X-MAC-CHINESESIMP',
        '10010' => 'X-MAC-ROMANIAN',
        '10017' => 'X-MAC-UKRAINIAN',
        '10021' => 'X-MAC-THAI',
        '10029' => 'X-MAC-CE',
        '10079' => 'X-MAC-ICELANDIC',
        '10081' => 'X-MAC-TURKISH',
        '10082' => 'X-MAC-CROATIAN',
        '12000' => 'UTF-32',
        '12001' => 'UTF-32BE',
        '20000' => 'X-CHINESE-CNS',
        '20001' => 'X-CP20001',
        '20002' => 'X-CHINESE-ETEN',
        '20003' => 'X-CP20003',
        '20004' => 'X-CP20004',
        '20005' => 'X-CP20005',
        '20105' => 'X-IA5',
        '20106' => 'X-IA5-GERMAN',
        '20107' => 'X-IA5-SWEDISH',
        '20108' => 'X-IA5-NORWEGIAN',
        '20127' => 'ASCII',
        '20261' => 'X-CP20261',
        '20269' => 'X-CP20269',
        '20273' => 'IBM273',
        '20277' => 'IBM277',
        '20278' => 'IBM278',
        '20280' => 'IBM280',
        '20284' => 'IBM284',
        '20285' => 'IBM285',
        '20290' => 'IBM290',
        '20297' => 'IBM297',
        '20420' => 'IBM420',
        '20423' => 'IBM423',
        '20424' => 'IBM424',
        '20833' => 'X-EBCDIC-KOREANEXTENDED',
        '20838' => 'IBM-THAI',
        '20866' => 'KOI8-R',
        '20871' => 'IBM871',
        '20880' => 'IBM880',
        '20905' => 'IBM905',
        '20924' => 'IBM00924',
        '20932' => 'EUC-JP',
        '20936' => 'X-CP20936',
        '20949' => 'X-CP20949',
        '21025' => 'CP1025',
        '21027' => '',
        '21866' => 'KOI8-U',
        '28591' => 'ISO-8859-1',
        '28592' => 'ISO-8859-2',
        '28593' => 'ISO-8859-3',
        '28594' => 'ISO-8859-4',
        '28595' => 'ISO-8859-5',
        '28596' => 'ISO-8859-6',
        '28597' => 'ISO-8859-7',
        '28598' => 'ISO-8859-8',
        '28599' => 'ISO-8859-9',
        '28603' => 'ISO-8859-13',
        '28605' => 'ISO-8859-15',
        '29001' => 'X-EUROPA',
        '38598' => 'ISO-8859-8-I',
        '50220' => 'ISO-2022-JP',
        '50221' => 'CSISO2022JP',
        '50222' => 'ISO-2022-JP',
        '50225' => 'ISO-2022-KR',
        '50227' => 'X-CP50227',
        '50229' => '',
        '50930' => '',
        '50931' => '',
        '50933' => '',
        '50935' => '',
        '50936' => '',
        '50937' => '',
        '50939' => '',
        '51932' => 'EUC-JP',
        '51936' => 'EUC-CN',
        '51949' => 'EUC-KR',
        '51950' => '',
        '52936' => 'HZ-GB-2312',
        '54936' => 'GB18030',
        '57002' => 'X-ISCII-DE',
        '57003' => 'X-ISCII-BE',
        '57004' => 'X-ISCII-TA',
        '57005' => 'X-ISCII-TE',
        '57006' => 'X-ISCII-AS',
        '57007' => 'X-ISCII-OR',
        '57008' => 'X-ISCII-KA',
        '57009' => 'X-ISCII-MA',
        '57010' => 'X-ISCII-GU',
        '57011' => 'X-ISCII-PA',
        '65000' => 'UTF-7',
        '65001' => 'UTF-8',
    ];
}
