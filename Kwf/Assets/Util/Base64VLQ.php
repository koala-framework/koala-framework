<?php

/**
 * Encode / Decode Base64 VLQ.
 *
 * @author bspot
 */
class Kwf_Assets_Util_Base64VLQ
{

  public static $SHIFT = 5;
  public static $MASK = 0x1F; // == (1 << SHIFT) == 0b00011111
  public static $CONTINUATION_BIT = 0x20; // == (MASK - 1 ) == 0b00100000

  public static $CHAR_TO_INT = array();
  public static $INT_TO_CHAR = array();

  /**
   * Convert from a two-complement value to a value where the sign bit is
   * is placed in the least significant bit.  For example, as decimals:
   *   1 becomes 2 (10 binary), -1 becomes 3 (11 binary)
   *   2 becomes 4 (100 binary), -2 becomes 5 (101 binary)
   * We generate the value for 32 bit machines, hence
   *   -2147483648 becomes 1, not 4294967297,
   * even on a 64 bit machine.
  */
  public static function toVLQSigned($aValue) {
    return 0xffffffff & ($aValue < 0 ? ((-$aValue) << 1) + 1 : ($aValue << 1) + 0);
  }

  /**
   * Convert to a two-complement value from a value where the sign bit is
   * is placed in the least significant bit. For example, as decimals:
   *   2 (10 binary) becomes 1, 3 (11 binary) becomes -1
   *   4 (100 binary) becomes 2, 5 (101 binary) becomes -2
   * We assume that the value was generated with a 32 bit machine in mind.
   * Hence
   *   1 becomes -2147483648
   * even on a 64 bit machine.
   */
  public static function fromVLQSigned($aValue) {
    return $aValue & 1 ? self::zeroFill(~$aValue+2, 1) | (-1 - 0x7fffffff) : self::zeroFill($aValue, 1);
  }

  /**
   * Return the base 64 VLQ encoded value.
   */
  public static function encode($aValue) {
    $encoded = "";

    $vlq = self::toVLQSigned($aValue);

    do {
      $digit = $vlq & self::$MASK;
      $vlq = self::zeroFill($vlq, self::$SHIFT);
      if ($vlq > 0) {
        $digit |= self::$CONTINUATION_BIT;
      }
      $encoded .= self::base64Encode($digit);
    } while ($vlq > 0);

    return $encoded;
  }

  /**
   * Return the value decoded from base 64 VLQ.
   */
  public static function decode($encoded) {
    $vlq = 0;

    $i = 0;
    do {
      $digit = self::base64Decode($encoded[$i]);
      $vlq |= ($digit & self::$MASK) << ($i*self::$SHIFT);
      $i++;
    } while ($digit & self::$CONTINUATION_BIT);

    return array(
        'value' => self::fromVLQSigned($vlq),
        'rest' => substr($encoded, $i)
    );
  }

  /**
   * Right shift with zero fill.
   *
   * @param number $a number to shift
   * @param nunber $b number of bits to shift
   * @return number
   */
  public static function zeroFill($a, $b) {
    return ($a >= 0) ? ($a >> $b) : ($a >> $b) & (PHP_INT_MAX >> ($b-1));
  }

  /**
   * Encode single 6-bit digit as base64.
   *
   * @param number $number
   * @return string
   */
  public static function base64Encode($number) {
    if ($number < 0 || $number > 63) {
      throw new Exception("Must be between 0 and 63: " . $number);
    }
    return self::$INT_TO_CHAR[$number];
  }

  /**
   * Decode single 6-bit digit from base64
   *
   * @param string $char
   * @return number
   */
  public static function base64Decode($char) {
    if (!array_key_exists($char, self::$CHAR_TO_INT)) {
      throw new Exception("Not a valid base 64 digit: " . $char);
    }
    return self::$CHAR_TO_INT[$char];
  }
}

// Initialize char conversion table.
Kwf_Assets_Util_Base64VLQ::$CHAR_TO_INT = array();
Kwf_Assets_Util_Base64VLQ::$INT_TO_CHAR = array();

foreach (str_split('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/') as $i => $char) {
  Kwf_Assets_Util_Base64VLQ::$CHAR_TO_INT[$char] = $i;
  Kwf_Assets_Util_Base64VLQ::$INT_TO_CHAR[$i] = $char;
}
