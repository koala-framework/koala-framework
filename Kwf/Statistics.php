<?php
/**
 * Provides static methods for Cookie Opt-In/-Out Mechanisms
 *
 * Also works when there is no Opt-In Component
 * The same implementation exists in Javascript, so components can decide to react on Opt-In Values
 * in PHP or JavaScript
 *
 * @see Kwc_Statistics_CookieBeforePlugin, Kwc_Statistics_CookieAfterPlugin
 */
class Kwf_Statistics
{
    const OPT_IN = 'in';
    const OPT_OUT = 'out';
    private static $_cookieName = 'cookieOpt';

    public static function getDefaultOptValue(Kwf_Component_Data $data)
    {
        $ret = $data->getBaseProperty('statistics.defaultOptValue');
        if ($ret != self::OPT_IN && $ret != self::OPT_OUT) {
            throw new Kwf_Exception('statistics.defaultOptValue must be ' . self::OPT_IN . ' or ' . self::OPT_OUT);
        }
        return $ret;
    }

    public static function isUserOptIn(Kwf_Component_Data $data)
    {
        if (!self::issetUserOptValue()) {
            return self::getDefaultOptValue($data) == self::OPT_IN;
        } else {
            return self::getUserOptValue() == self::OPT_IN;
        }
    }

    public static function issetUserOptValue()
    {
        return isset($_COOKIE[self::$_cookieName]);
    }

    public static function getUserOptValue()
    {
        if (!isset($_COOKIE[self::$_cookieName])) {
            return null;
        } else {
            $ret = $_COOKIE[self::$_cookieName];
            if ($ret != self::OPT_IN && $ret != self::OPT_OUT) {
                $exception = new Kwf_Exception('stored Cookie must be ' . self::OPT_IN . ' or ' . self::OPT_OUT);
                $exception->logOrThrow();
                return null;
            }
            return $ret;
        }
    }

    public static function setUserOptValue($value)
    {
        if ($value != self::OPT_IN && $value != self::OPT_OUT) {
            throw new Kwf_Exception('$value must be ' . self::OPT_IN . ' or ' . self::OPT_OUT);
        }
        setcookie(self::$_cookieName, $value, time() + (3*365*24*60*60), '/');
    }
}

