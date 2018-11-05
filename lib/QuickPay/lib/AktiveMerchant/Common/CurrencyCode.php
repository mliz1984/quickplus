<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace AktiveMerchant\Common;

/**
 * Allows to access the numeric value of a currency from the given ISO 4217
 * currency code.
 *
 * @package Aktive-Merchant
 * @author  Andreas Kollaros
 * @license MIT {@link http://opensource.org/licenses/mit-license.php}
 */
class CurrencyCode implements \ArrayAccess
{

    /* -(  ArrayAccess  )--------------------------------------------------- */

    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->CURRENCY_CODES);
    }

    public function offsetGet($offset)
    {
        return $this->CURRENCY_CODES[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->CURRENCY_CODES[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        if ($this->offsetExists($offset)) {
            unset($this->CURRENCY_CODES[$offset]);
        }
    }

    /**
     * An array mapping the ISO 4217 code of a country with its numeric value.
     *
     * @var array
     * @access private
     */
    private $CURRENCY_CODES = array(
        "XPT" => "962",
        "SAR" => "682",
        "RUB" => "643",
        "NIO" => "558",
        "LAK" => "418",
        "NOK" => "578",
        "USD" => "840",
        "XCD" => "951",
        "OMR" => "512",
        "AMD" => "051",
        "CDF" => "976",
        "KPW" => "408",
        "CNY" => "156",
        "KES" => "404",
        "PLN" => "985",
        "KHR" => "116",
        "MVR" => "462",
        "GTQ" => "320",
        "CLP" => "152",
        "INR" => "356",
        "BZD" => "084",
        "MYR" => "458",
        "GWP" => "624",
        "HKD" => "344",
        "SEK" => "752",
        "COP" => "170",
        "DKK" => "208",
        "BYR" => "974",
        "LYD" => "434",
        "UYI" => "940",
        "RON" => "946",
        "DZD" => "012",
        "BIF" => "108",
        "ARS" => "032",
        "GIP" => "292",
        "BOB" => "068",
        "USN" => "997",
        "AED" => "784",
        "STD" => "678",
        "PGK" => "598",
        "NGN" => "566",
        "XOF" => "952",
        "ERN" => "232",
        "MWK" => "454",
        "CUP" => "192",
        "GMD" => "270",
        "ZWL" => "932",
        "TZS" => "834",
        "CVE" => "132",
        "COU" => "970",
        "BTN" => "064",
        "UGX" => "800",
        "SYP" => "760",
        "MNT" => "496",
        "MAD" => "504",
        "LSL" => "426",
        "XAF" => "950",
        "XTS" => "963",
        "XAG" => "961",
        "TOP" => "776",
        "RSD" => "941",
        "SHP" => "654",
        "HTG" => "332",
        "MGA" => "969",
        "USS" => "998",
        "MZN" => "943",
        "LVL" => "428",
        "FKP" => "238",
        "CHE" => "947",
        "BWP" => "072",
        "HNL" => "340",
        "EUR" => "978",
        "PYG" => "600",
        "EGP" => "818",
        "CHF" => "756",
        "ILS" => "376",
        "LBP" => "422",
        "ANG" => "532",
        "KZT" => "398",
        "WST" => "882",
        "GYD" => "328",
        "THB" => "764",
        "NPR" => "524",
        "KMF" => "174",
        "IRR" => "364",
        "XPD" => "964",
        "XBA" => "955",
        "UYU" => "858",
        "SRD" => "968",
        "JPY" => "392",
        "BRL" => "986",
        "XBB" => "956",
        "SZL" => "748",
        "MOP" => "446",
        "BMD" => "060",
        "XBC" => "957",
        "ETB" => "230",
        "JOD" => "400",
        "IDR" => "360",
        "EEK" => "233",
        "MDL" => "498",
        "XPF" => "953",
        "MRO" => "478",
        "XBD" => "958",
        "YER" => "886",
        "PEN" => "604",
        "BAM" => "977",
        "AWG" => "533",
        "NZD" => "554",
        "VEF" => "937",
        "TRY" => "949",
        "SLL" => "694",
        "KYD" => "136",
        "AOA" => "973",
        "TND" => "788",
        "TJS" => "972",
        "LKR" => "144",
        "SGD" => "702",
        "SCR" => "690",
        "MXN" => "484",
        "LTL" => "440",
        "HUF" => "348",
        "DJF" => "262",
        "BSD" => "044",
        "GNF" => "324",
        "ISK" => "352",
        "VUV" => "548",
        "SDG" => "938",
        "GEL" => "981",
        "FJD" => "242",
        "DOP" => "214",
        "XDR" => "960",
        "PHP" => "608",
        "MUR" => "480",
        "MMK" => "104",
        "KRW" => "410",
        "LRD" => "430",
        "BBD" => "052",
        "XAU" => "959",
        "ZMK" => "894",
        "VND" => "704",
        "UAH" => "980",
        "TMT" => "934",
        "IQD" => "368",
        "BGN" => "975",
        "GBP" => "826",
        "KGS" => "417",
        "ZAR" => "710",
        "TTD" => "780",
        "HRK" => "191",
        "BOV" => "984",
        "RWF" => "646",
        "CLF" => "990",
        "BHD" => "048",
        "UZS" => "860",
        "TWD" => "901",
        "PKR" => "586",
        "CRC" => "188",
        "AUD" => "036",
        "MKD" => "807",
        "AFN" => "971",
        "NAD" => "516",
        "BDT" => "050",
        "AZN" => "944",
        "CZK" => "203",
        "XXX" => "999",
        "CHW" => "948",
        "SOS" => "706",
        "QAR" => "634",
        "PAB" => "590",
        "CUC" => "931",
        "MXV" => "979",
        "SBD" => "090",
        "SVC" => "222",
        "ALL" => "008",
        "BND" => "096",
        "JMD" => "388",
        "CAD" => "124",
        "KWD" => "414",
        "GHS" => "936"
    );
}
