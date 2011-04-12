<?php
/**
 *  Kizano_Strings
 *
 *  LICENSE
 *
 *  This source file is subject to the new BSD license that is bundled
 *  with this package in the file LICENSE.txt.
 *  It is also available through the world-wide-web at this URL:
 *  http://framework.zend.com/license/new-bsd
 *  If you did not receive a copy of the license and are unable to
 *  obtain it through the world-wide-web, please send an email
 *  to license@zend.com so we can send you a copy immediately.
 *
 *  @category   Kizano
 *  @package    Strings
 *  @copyright  Copyright (c) 2009-2011 Markizano Draconus <markizano@markizano.net>
 *  @license    http://framework.zend.com/license/new-bsd     New BSD License
 *  @author     Markizano Draconus <markizano@markizano.net>
 */

/**
 *  Misc. class for handling string manipulation that PHP doesn't natively perform.
 *
 *  @category   Kizano
 *  @package    Strings
 *  @copyright  Copyright (c) 2009-2011 Markizano Draconus <markizano@markizano.net>
 *  @license    http://framework.zend.com/license/new-bsd     New BSD License
 *  @author     Markizano Draconus <markizano@markizano.net>
 */
class Kizano_Strings
{

    # Universal delimiter
    const DELIMIT = '<!>';

    # Universal Salt
    const SALT = '8E19A1EA351F268CC635D3DD96D3C6A754266D33';

    public static $STATES = array(
        'AT' => array(
            'ACT' => 'Australian Capital Territory',
            'NSW' => 'New South Wales',
            'NT' => 'Northern Territory',
            'QLD' => 'Queensland',
            'SA' => 'South Australia',
            'TAS' => 'Tasmania',
            'VIC' => 'Victoria',
            'WA' => 'Western Australia',
        ),
        'CA' => array(
            'AB' => 'Alberta',
            'BC' => 'British Columbia',
            'MB' => 'Manitoba',
            'NB' => 'New Brunswick',
            'NL' => 'Newfoundland and Labrador',
            'NT' => 'Northwest Territories',
            'NS' => 'Nova Scotia',
            'NU' => 'Nunavut',
            'ON' => 'Ontario',
            'PE' => 'Prince Edward Island',
            'QC' => 'Quebec',
            'SK' => 'Saskatchewan',
            'YT' => 'Yukon',
        ),
        'UK' => array(
            'AB' => 'Aberdeen',
            'AL' => 'St. Albans',
            'B' => 'Birmingham',
            'BA' => 'Bath',
            'BB' => 'Blackburn',
            'BD' => 'Bradford',
            'BH' => 'Bournemouth',
            'BL' => 'Bolton',
            'BN' => 'Brighton',
            'BR' => 'Bromley',
            'BS' => 'Bristol',
            'BT' => 'Belfast',
            'CA' => 'Carlisle',
            'CB' => 'Cambridge',
            'CF' => 'Cardiff',
            'CH' => 'Chester',
            'CM' => 'Chelmsford',
            'CO' => 'Colchester',
            'CR' => 'Croydon',
            'CT' => 'Canterbury',
            'CV' => 'Coventry',
            'CW' => 'Crewe',
            'DA' => 'Dartford',
            'DD' => 'Dundee',
            'DE' => 'Derby',
            'DG' => 'Dumfries',
            'DH' => 'Durham',
            'DL' => 'Darlington',
            'DN' => 'Doncaster',
            'DT' => 'Dorchester',
            'DY' => 'Dudley',
            'E' => 'London E',
            'EC' => 'London EC',
            'EH' => 'Edinburgh',
            'EN' => 'Enfield',
            'EX' => 'Exeter',
            'FK' => 'Falkirk',
            'FY' => 'Blackpool',
            'G' => 'Glasgow',
            'GL' => 'Gloucester',
            'GU' => 'Guildford',
            'HA' => 'Harrow',
            'HD' => 'Huddersfield',
            'HG' => 'Harrogate',
            'HP' => 'Hemel Hempstead',
            'HR' => 'Hereford',
            'HS' => 'Outer Hebrides',
            'HU' => 'Hull',
            'HX' => 'Halifax',
            'IG' => 'Ilford',
            'IP' => 'Ipswich',
            'IV' => 'Inverness',
            'KA' => 'Kilmarnock',
            'KT' => 'Kingston upon Thames',
            'KW' => 'Kirkwall',
            'KY' => 'Kirkcaldy',
            'L' => 'Liverpool',
            'LA' => 'Lancaster',
            'LD' => 'Llandrindod Wells',
            'LE' => 'Leicester',
            'LL' => 'Llandudno',
            'LN' => 'Lincoln',
            'LS' => 'Leeds',
            'LU' => 'Luton',
            'M' => 'Manchester',
            'ME' => 'Rochester',
            'MK' => 'Milton Keynes',
            'ML' => 'Motherwell',
            'N' => 'London N',
            'NE' => 'Newcastle upon Tyne',
            'NG' => 'Nottingham',
            'NN' => 'Northampton',
            'NP' => 'Newport',
            'NR' => 'Norwick',
            'NW' => 'Londong NW',
            'OL' => 'Oldham',
            'OX' => 'Oxford',
            'PA' => 'Paisley',
            'PE' => 'Peterborough',
            'PH' => 'Perth',
            'PL' => 'Plymouth',
            'PO' => 'Portsmouth',
            'PR' => 'Preston',
            'RG' => 'Reading',
            'RH' => 'Redhill',
            'RM' => 'Romford',
            'S' => 'Sheffield',
            'SA' => 'Swansea',
            'SE' => 'London SE',
            'SG' => 'Stevenage',
            'SK' => 'Stockport',
            'SL' => 'Slough',
            'SM' => 'Sutton',
            'SN' => 'Swindon',
            'SO' => 'Southampton',
            'SP' => 'Salisbury',
            'SR' => 'Sunderland',
            'SS' => 'Southend on Sea',
            'ST' => 'Soke-on-Trent',
            'SW' => 'London SW',
            'SY' => 'Shrewsbury',
            'TA' => 'Taunton',
            'TD' => 'Galashiels',
            'TF' => 'Telford',
            'TN' => 'Tonbridge',
            'TQ' => 'Torquay',
            'TR' => 'Truro',
            'TS' => 'Cleveland',
            'TW' => 'Twickenham',
            'UB' => 'Southall',
            'W' => 'London W',
            'WA' => 'Warrington',
            'WC' => 'London WC',
            'WD' => 'Watford',
            'WF' => 'Wakefield',
            'WN' => 'Wigan',
            'WR' => 'Worcester',
            'WS' => 'Walsall',
            'WV' => 'Wolverhampton',
            'YO' => 'York',
            'ZE' => 'Lerwick',
        ),
        'US' => array(
            '' => '<--Select your State-->',
            'AL' => 'AL - Alabama',
            'AK' => 'AK - Alaska',
            'AZ' => 'AZ - Arizona',
            'AR' => 'AR - Arkansas',
            'CA' => 'CA - California',
            'CO' => 'CO - Colorado',
            'CT' => 'CT - Connecticut',
            'DE' => 'DE - Delaware',
            'DC' => 'DC - District of Columbia',
            'FL' => 'FL - Florida',
            'GA' => 'GA - Georgia',
            'HI' => 'HI - Hawaii',
            'ID' => 'ID - Idaho',
            'IL' => 'IL - Illinois',
            'IN' => 'IN - Indiana',
            'IA' => 'IA - Iowa',
            'KS' => 'KS - Kansas',
            'KY' => 'KY - Kentucky',
            'LA' => 'LA - Louisiana',
            'ME' => 'ME - Maine',
            'MD' => 'MD - Maryland',
            'MA' => 'MA - Massachusetts',
            'MI' => 'MI - Michigan',
            'MN' => 'MN - Minnesota',
            'MS' => 'MS - Mississippi',
            'MO' => 'MO - Missouri',
            'MT' => 'MT - Montana',
            'NE' => 'NE - Nebraska',
            'NV' => 'NV - Nevada',
            'NH' => 'NH - New Hampshire',
            'NJ' => 'NJ - New Jersey',
            'NM' => 'NM - New Mexico',
            'NY' => 'NY - New York',
            'NC' => 'NC - North Carolina',
            'ND' => 'ND - North Dakota',
            'OH' => 'OH - Ohio',
            'OK' => 'OK - Oklahoma',
            'OR' => 'OR - Oregon',
            'PA' => 'PE - Pennsylvania',
            'RI' => 'RI - Rhode Island',
            'SC' => 'SC - South Carolina',
            'SD' => 'SD - South Dakota',
            'TN' => 'TN - Tennessee',
            'TX' => 'TX - Texas',
            'UT' => 'UT - Utah',
            'VT' => 'VT - Vermont',
            'VA' => 'VA - Virginia',
            'WA' => 'WA - Washington',
            'WV' => 'WV - West Virginia',
            'WI' => 'WI - Wisconsin',
            'WY' => 'WY - Wyoming',
        )
    );

    public static $COUNTRIES = array(
        '' => '<-- Select Country -->',
        'US' => 'United States',
        'AF' => 'Afghanistan',
        'AL' => 'Albania',
        'DZ' => 'Algeria',
        'AS' => 'American Samoa',
        'AD' => 'Andorra',
        'AO' => 'Angola',
        'AI' => 'Anguilla',
        'AQ' => 'Antarctica',
        'AG' => 'Antigua and Barbuda',
        'AR' => 'Argentina',
        'AM' => 'Armenia',
        'AW' => 'Aruba',
        'AU' => 'Australia',
        'AT' => 'Austria',
        'AZ' => 'Azerbaijan',
        'BS' => 'Bahamas',
        'BH' => 'Bahrain',
        'BD' => 'Bangladesh',
        'BB' => 'Barbados',
        'BY' => 'Belarus',
        'BE' => 'Belgium',
        'BZ' => 'Belize',
        'BJ' => 'Benin',
        'BM' => 'Bermuda',
        'BT' => 'Bhutan',
        'BO' => 'Bolivia',
        'BA' => 'Bosnia and Herzegovina',
        'BW' => 'Botswana',
        'BV' => 'Bouvet Island',
        'BR' => 'Brazil',
        'IO' => 'British Indian Ocean',
        'BN' => 'Brunei Darussalam',
        'BG' => 'Bulgaria',
        'BF' => 'Burkina Faso',
        'BI' => 'Burundi',
        'KH' => 'Cambodia',
        'CM' => 'Cameroon',
        'CA' => 'Canada',
        'CV' => 'Cape Verde',
        'KY' => 'Cayman Islands',
        'CF' => 'Central African Republic',
        'TD' => 'Chad',
        'CL' => 'Chile',
        'CN' => 'China',
        'CX' => 'Christmas Island',
        'CC' => 'Cocos (Keeling) Islands',
        'CO' => 'Colombia',
        'KM' => 'Comoros',
        'CG' => 'Congo',
        'CD' => 'Congo, the Democratic Republic of the',
        'CD' => 'Republic of Congo',
        'CK' => 'Cook Islands',
        'CR' => 'Costa Rica',
        'CI' => 'Cote D\'Ivoire',
        'HR' => 'Croatia',
        'CU' => 'Cuba',
        'CY' => 'Cyprus',
        'CZ' => 'Czech Republic',
        'DK' => 'Denmark',
        'DJ' => 'Djibouti',
        'DM' => 'Dominica',
        'DO' => 'Dominican Republic',
        'EC' => 'Ecuador',
        'EG' => 'Egypt',
        'SV' => 'El Salvador',
        'GQ' => 'Equatorial Guinea',
        'ER' => 'Eritrea',
        'EE' => 'Estonia',
        'ET' => 'Ethiopia',
        'FK' => 'Falkland Islands',
        'FO' => 'Faroe Islands',
        'FJ' => 'Fiji',
        'FI' => 'Finland',
        'FR' => 'France',
        'GF' => 'French Guiana',
        'PF' => 'French Polynesia',
        'TF' => 'French Southern Territories',
        'GA' => 'Gabon',
        'GM' => 'Gambia',
        'GE' => 'Georgia',
        'DE' => 'Germany',
        'GH' => 'Ghana',
        'GI' => 'Gibraltar',
        'GR' => 'Greece',
        'GL' => 'Greenland',
        'GD' => 'Grenada',
        'GP' => 'Guadeloupe',
        'GU' => 'Guam',
        'GT' => 'Guatemala',
        'GN' => 'Guinea',
        'GW' => 'Guinea-Bissau',
        'GY' => 'Guyana',
        'HT' => 'Haiti',
        'HM' => 'Heard Islands',
        'HM' => 'Mcdonald Islands',
        'VA' => 'Holy See',
        'HN' => 'Honduras',
        'HK' => 'Hong Kong',
        'HU' => 'Hungary',
        'IS' => 'Iceland',
        'IN' => 'India',
        'ID' => 'Indonesia',
        'IR' => 'Iran',
        'IQ' => 'Iraq',
        'IE' => 'Ireland',
        'IL' => 'Israel',
        'IT' => 'Italy',
        'JM' => 'Jamaica',
        'JP' => 'Japan',
        'JO' => 'Jordan',
        'KZ' => 'Kazakhstan',
        'KE' => 'Kenya',
        'KI' => 'Kiribati',
        'KP' => 'Korea, South',
        'KR' => 'Korea, North',
        'KW' => 'Kuwait',
        'KG' => 'Kyrgyzstan',
        'LA' => 'Lao People\'s Republic',
        'LV' => 'Latvia',
        'LB' => 'Lebanon',
        'LS' => 'Lesotho',
        'LR' => 'Liberia',
        'LY' => 'Libyan Arab Jamahiriya',
        'LI' => 'Liechtenstein',
        'LT' => 'Lithuania',
        'LU' => 'Luxembourg',
        'MO' => 'Macao',
        'MK' => 'Macedonia',
        'MG' => 'Madagascar',
        'MW' => 'Malawi',
        'MY' => 'Malaysia',
        'MV' => 'Maldives',
        'ML' => 'Mali',
        'MT' => 'Malta',
        'MH' => 'Marshall Islands',
        'MQ' => 'Martinique',
        'MR' => 'Mauritania',
        'MU' => 'Mauritius',
        'YT' => 'Mayotte',
        'MX' => 'Mexico',
        'FM' => 'Micronesia',
        'MD' => 'Moldova, Republic of',
        'MC' => 'Monaco',
        'MN' => 'Mongolia',
        'MS' => 'Montserrat',
        'MA' => 'Morocco',
        'MZ' => 'Mozambique',
        'MM' => 'Myanmar',
        'NA' => 'Namibia',
        'NR' => 'Nauru',
        'NP' => 'Nepal',
        'NL' => 'Netherlands',
        'AN' => 'Netherlands Antilles',
        'NC' => 'New Caledonia',
        'NZ' => 'New Zealand',
        'NI' => 'Nicaragua',
        'NE' => 'Niger',
        'NG' => 'Nigeria',
        'NU' => 'Niue',
        'NF' => 'Norfolk Island',
        'MP' => 'Northern Mariana Islands',
        'NO' => 'Norway',
        'OM' => 'Oman',
        'PK' => 'Pakistan',
        'PW' => 'Palau',
        'PS' => 'Palestinian Territory',
        'PA' => 'Panama',
        'PG' => 'Papua New Guinea',
        'PY' => 'Paraguay',
        'PE' => 'Peru',
        'PH' => 'Philippines',
        'PN' => 'Pitcairn',
        'PL' => 'Poland',
        'PT' => 'Portugal',
        'PR' => 'Puerto Rico',
        'QA' => 'Qatar',
        'RE' => 'Reunion',
        'RO' => 'Romania',
        'RU' => 'Russian Federation',
        'RW' => 'Rwanda',
        'SH' => 'Saint Helena',
        'KN' => 'Saint Kitts and Nevis',
        'LC' => 'Saint Lucia',
        'PM' => 'Saint Pierre and Miquelon',
        'VC' => 'Saint Vincent',
        'VC' => 'The Grenadines',
        'WS' => 'Samoa',
        'SM' => 'San Marino',
        'ST' => 'Sao Tome and Principe',
        'SA' => 'Saudi Arabia',
        'SN' => 'Senegal',
        'CS' => 'Serbia and Montenegro',
        'SC' => 'Seychelles',
        'SL' => 'Sierra Leone',
        'SG' => 'Singapore',
        'SK' => 'Slovakia',
        'SI' => 'Slovenia',
        'SB' => 'Solomon Islands',
        'SO' => 'Somalia',
        'ZA' => 'South Africa',
        'GS' => 'South Sandwich Islands',
        'GS' => 'South Georgia',
        'ES' => 'Spain',
        'LK' => 'Sri Lanka',
        'SD' => 'Sudan',
        'SR' => 'Suriname',
        'SJ' => 'Svalbard and Jan Mayen',
        'SZ' => 'Swaziland',
        'SE' => 'Sweden',
        'CH' => 'Switzerland',
        'SY' => 'Syrian Arab Republic',
        'TW' => 'Taiwan',
        'TJ' => 'Tajikistan',
        'TZ' => 'Tanzania',
        'TH' => 'Thailand',
        'TL' => 'Timor-Leste',
        'TG' => 'Togo',
        'TK' => 'Tokelau',
        'TO' => 'Tonga',
        'TT' => 'Trinidad and Tobago',
        'TN' => 'Tunisia',
        'TR' => 'Turkey',
        'TM' => 'Turkmenistan',
        'TC' => 'Turks and Caicos Islands',
        'TV' => 'Tuvalu',
        'UG' => 'Uganda',
        'UA' => 'Ukraine',
        'AE' => 'United Arab Emirates',
        'GB' => 'United Kingdom',
        'US' => 'United States',
        'UM' => 'US Minor Outlying Islands',
        'UY' => 'Uruguay',
        'UZ' => 'Uzbekistan',
        'VU' => 'Vanuatu',
        'VE' => 'Venezuela',
        'VN' => 'Viet Nam',
        'VG' => 'Virgin Islands, British',
        'VI' => 'Virgin Islands, U.s.',
        'WF' => 'Wallis and Futuna',
        'EH' => 'Western Sahara',
        'YE' => 'Yemen',
        'ZM' => 'Zambia',
        'ZW' => 'Zimbabwe',
    );

    public static $DAYS = array(
        'Sunday',
        'Monday',
        'Tuesday',
        'Wednesday',
        'Thursday',
        'Friday',
        'Saturday',
        'Sunday',
    );

    public static $MONTHS = array(
        'January',
        'February',
        'March',
        'April',
        'May',
        'June',
        'July',
        'August',
        'September',
        'October',
        'November',
        'December',
    );

    /**
     *  Prevents instantiation of this class.
     *  
     *  @return void
     */
    protected function __construct()
    {}

    /**
     *  Prevents cloning an instance of this class.
     *  
     *  @return void
     */
    protected function __clone()
    {}

    /**
     * Creates a slug from any input.
     *  
     * @param    name    String    The input to create a slug
     *  
     * @return            String    The Slug.
     */
    public static function sluggify($name)
    {
        if (!is_string($name)) {
            throw new InvalidArgumentException('Argument 1 ($name) is expected string.');
        }

        $slug = preg_replace('/(\s+)/', '_', trim($name));
        return preg_replace('/[^\s\w\d\.]+/i', '-', $slug);
    }

    /**
     * Crates a random hexidecimal string of a specified length
     *  
     * @param    Len    Int        The length of the random hex
     *  
     * @return        String    The random hex string representation in capitals
     */
    public static function strRandHex($Len)
    {
        $RandStr = "0123456789ABCDEF";
        $result = "";
        for($i = 0; $i < $Len; $i++) {
            $result .= substr($RandStr, mt_rand(0, strlen($RandStr) -1), 1);
        }
        if(! isset($result{$Len -1}))
            $result .= substr($RandStr, mt_rand(0, strlen($RandStr) -1), 1);
        return $result;
    }

    /**
     * Cuts a string in half and returns an array of the result
     *  
     * @param string        String    The string to cut
     *  
     * @return                Array    The string cut in half
     */
    public static function half($string)
    {
        $left = subStr($string, 0, strLen($string) / 2);
        $right = subStr($string, strLen($string) / 2);
        return array($left, $right);
    }

    /**
     * Gives us our standard hash of a password. Every single call to a hash would call this function
     * in some way.
     *  
     * @param    plaintext    String    The plaintext password
     * @param    salt        String    A random salt that will be stored with this password
     *  
     * @return                String    The securely salted hashed password.
     */
    public static function hashPass($plaintext, $salt)
    {
        return strToUpper(hash('sha256', $salt.self::SALT.hash('haval256,5', $salt.$plaintext.self::SALT.hash('tiger160,4', $salt.$plaintext.self::SALT).$salt).$salt));
    }

    /**
     * Gets all the flash messages that have been run since the last session flush.
     *  
     * @returns    String        The flash messages in linear format
     */
    public static function getFlash()
    {
        $session = Zend_Registry::get('session');
        $result = is_array($session->flash)? nl2br(join(chr(10), (array)$session->flash)): null;
        unset($session->flash);
        return $result;
    }

    /**
     * Create a flash message entry.
     *  
     * @param    msg        String        The message to store
     *  
     * @return         void
     */
    public static function flash($msg)
    {
        $session = Zend_Registry::get('session');
        $session->flash[] = $msg;
        return;
    }

    /**
     * @Description: Gets a line from standard input
     *  
     * @param    len        Int        The number of characters that should be read from standard in (if 0, keep going until EOF)
     *  
     * @return            String    Info from standar input
     */
    public static function readInput($len = 0)
    {
        $f = fOpen('php://stdin', 'r');
            $result = null;
            while(!fEOF($f))
                if((int)$len && strLen($result) > $len) break;
                else $result .= fGetc($f);
        fClose($f);
        return $result;
    }
}

