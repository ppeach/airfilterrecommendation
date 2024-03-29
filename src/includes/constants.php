<?php
# collection of key terms so we only have to change in one place
# Variable names are in uppercase following Python convention for this - no idea
# if it applies to PHP, but it should

# Machine-readable stuff first - all lowercase please

# Basic yes and no, for boolean questions but we're not using boolean types here
$VALUE_YES = 'yes';
$VALUE_NO = 'no';

# Array ACH options and values
$ACH_OPTIONS = array(
    '4' => '4 Air Changes per Hour (ACH)',
    '6' => '6 Air Changes per Hour (ACH)',
    '9' => '9 Air Changes per Hour (ACH)',
);

# Array L/p/s options and values
$LPS_OPTIONS = array(
    '10' => '10 L/p/s (Minimum, WHO recommendation)',
	'15' => '15 L/p/s (Office)',
    '20' => '20 L/p/s (Retail, Daycare, School, Healthcare exam room)',
	'25' => '25 L/p/s (Residential common space, Lecture hall, Lobbies)',
	'30' => '30 L/p/s (Restaurant, Convention)',
	'40' => '40 L/p/s (Gym)',
    '45' => '45 L/p/s (Healthcare waiting room)'
);

# Measurements
$MEASUREMENT_OPTIONS = array(
    'm3' => 'm3',
    'cubic' => 'cubic feet'
);

# Human-readable now
$DISPLAY_SCHEDULE_YES = 'Yes';
$DISPLAY_SCHEDULE_NO = 'Not necessary';

$DISPLAY_DIY_YES = 'Yes';
$DISPLAY_DIY_NO = 'No';

$DISPLAY_WIFI_YES = 'Yes';
$DISPLAY_WIFI_NO = 'Not necessary';

$DISPLAY_PREFILTER_YES = 'Yes';
$DISPLAY_PREFILTER_NO = 'Not necessary';

# Filter replacement schedule
$FRS_OPTIONS = array(
    '6' => 'Every 6 months',
    '12' => 'Every 12 months',
    '24' => 'Every 24 months'
);

# Assumed filter lifetime
$AFL_OPTIONS = array(
    '1' => '1 year',
    '2' => '2 years',
    '3' => '3 years',
    '4' => '4 years',
    '5' => '5 years',
    '6' => '6 years',
    '7' => '7 years',
    '8' => '8 years',
    '9' => '9 years',
    '10' => '10 years'
);

# SVG Icons
$SVG_INFO = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="blue" class="bi bi-info-circle-fill" viewBox="0 0 16 16"><path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"></path></svg>';

# Admin menu
$ADMIN_MENU = array(
    'dashboard' => '/admin/index.php',
    'analytics' => '/admin/views/analytics.php',
    'settings' => '/admin/views/settings.php'
);

# Currency symbols
$CURRENCY_SYMBOLS = array(
    'AED' => 'د.إ',
    'AFN' => '؋',
    'ALL' => 'L',
    'AMD' => '֏',
    'ANG' => 'ƒ',
    'AOA' => 'Kz',
    'ARS' => '$',
    'AUD' => '$',
    'AWG' => 'ƒ',
    'AZN' => '₼',
    'BAM' => 'KM',
    'BBD' => '$',
    'BDT' => '৳',
    'BGN' => 'лв',
    'BHD' => '.د.ب',
    'BIF' => 'FBu',
    'BMD' => '$',
    'BND' => '$',
    'BOB' => '$b',
    'BRL' => 'R$',
    'BSD' => '$',
    'BTC' => '฿',
    'BTN' => 'Nu.',
    'BWP' => 'P',
    'BYR' => 'Br',
    'BYN' => 'Br',
    'BZD' => 'BZ$',
    'CAD' => '$',
    'CDF' => 'FC',
    'CHF' => 'CHF',
    'CLP' => '$',
    'CNY' => '¥',
    'COP' => '$',
    'CRC' => '₡',
    'CUC' => '$',
    'CUP' => '₱',
    'CVE' => '$',
    'CZK' => 'Kč',
    'DJF' => 'Fdj',
    'DKK' => 'kr',
    'DOP' => 'RD$',
    'DZD' => 'دج',
    'EEK' => 'kr',
    'EGP' => '£',
    'ERN' => 'Nfk',
    'ETB' => 'Br',
    'ETH' => 'Ξ',
    'EUR' => '€',
    'FJD' => '$',
    'FKP' => '£',
    'GBP' => '£',
    'GEL' => '₾',
    'GGP' => '£',
    'GHC' => '₵',
    'GHS' => 'GH₵',
    'GIP' => '£',
    'GMD' => 'D',
    'GNF' => 'FG',
    'GTQ' => 'Q',
    'GYD' => '$',
    'HKD' => '$',
    'HNL' => 'L',
    'HRK' => 'kn',
    'HTG' => 'G',
    'HUF' => 'Ft',
    'IDR' => 'Rp',
    'ILS' => '₪',
    'IMP' => '£',
    'INR' => '₹',
    'IQD' => 'ع.د',
    'IRR' => '﷼',
    'ISK' => 'kr',
    'JEP' => '£',
    'JMD' => 'J$',
    'JOD' => 'JD',
    'JPY' => '¥',
    'KES' => 'KSh',
    'KGS' => 'лв',
    'KHR' => '៛',
    'KMF' => 'CF',
    'KPW' => '₩',
    'KRW' => '₩',
    'KWD' => 'KD',
    'KYD' => '$',
    'KZT' => 'лв',
    'LAK' => '₭',
    'LBP' => '£',
    'LKR' => '₨',
    'LRD' => '$',
    'LSL' => 'M',
    'LTC' => 'Ł',
    'LTL' => 'Lt',
    'LVL' => 'Ls',
    'LYD' => 'LD',
    'MAD' => 'MAD',
    'MDL' => 'lei',
    'MGA' => 'Ar',
    'MKD' => 'ден',
    'MMK' => 'K',
    'MNT' => '₮',
    'MOP' => 'MOP$',
    'MRO' => 'UM',
    'MRU' => 'UM',
    'MUR' => '₨',
    'MVR' => 'Rf',
    'MWK' => 'MK',
    'MXN' => '$',
    'MYR' => 'RM',
    'MZN' => 'MT',
    'NAD' => '$',
    'NGN' => '₦',
    'NIO' => 'C$',
    'NOK' => 'kr',
    'NPR' => '₨',
    'NZD' => '$',
    'OMR' => '﷼',
    'PAB' => 'B/.',
    'PEN' => 'S/.',
    'PGK' => 'K',
    'PHP' => '₱',
    'PKR' => '₨',
    'PLN' => 'zł',
    'PYG' => 'Gs',
    'QAR' => '﷼',
    'RMB' => '￥',
    'RON' => 'lei',
    'RSD' => 'Дин.',
    'RUB' => '₽',
    'RWF' => 'R₣',
    'SAR' => '﷼',
    'SBD' => '$',
    'SCR' => '₨',
    'SDG' => 'ج.س.',
    'SEK' => 'kr',
    'SGD' => '$',
    'SHP' => '£',
    'SLL' => 'Le',
    'SOS' => 'S',
    'SRD' => '$',
    'SSP' => '£',
    'STD' => 'Db',
    'STN' => 'Db',
    'SVC' => '$',
    'SYP' => '£',
    'SZL' => 'E',
    'THB' => '฿',
    'TJS' => 'SM',
    'TMT' => 'T',
    'TND' => 'د.ت',
    'TOP' => 'T$',
    'TRL' => '₤',
    'TRY' => '₺',
    'TTD' => 'TT$',
    'TVD' => '$',
    'TWD' => 'NT$',
    'TZS' => 'TSh',
    'UAH' => '₴',
    'UGX' => 'USh',
    'USD' => '$',
    'UYU' => '$U',
    'UZS' => 'лв',
    'VEF' => 'Bs',
    'VND' => '₫',
    'VUV' => 'VT',
    'WST' => 'WS$',
    'XAF' => 'FCFA',
    'XBT' => 'Ƀ',
    'XCD' => '$',
    'XOF' => 'CFA',
    'XPF' => '₣',
    'YER' => '﷼',
    'ZAR' => 'R',
    'ZWD' => 'Z$'
);

?>
