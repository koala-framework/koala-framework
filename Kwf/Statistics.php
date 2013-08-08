<?php
class Kwf_Statistics
{
    const OPT_VALUE_IN = 'in';
    const OPT_VALUE_OUT = 'out';
    const OPT_TYPE_IN = 'opt-in';
    const OPT_TYPE_OUT = 'opt-out';
    private static $_cookieName = 'cookieOpt';

    public static function getOptType($data)
    {
        if ($data instanceof Kwf_Component_Data) {
            $ret = $data->getBaseProperty('statistics.optType');
        } else {
            $ret = (string)$data;
        }
        if ($ret != self::OPT_TYPE_IN && $ret != self::OPT_TYPE_OUT) {
            throw new Kwf_Exception('statistics.optType must be ' . self::OPT_TYPE_IN . ' or ' . self::OPT_TYPE_OUT);
        }
        return $ret;
    }

    public static function isOptedIn($data)
    {
        if (!self::hasOpted()) {
            return self::getOptType($data) == self::OPT_TYPE_OUT;
        } else {
            return self::getOptedValue() == self::OPT_VALUE_IN;
        }
    }

    public static function hasOpted()
    {
        return isset($_COOKIE[self::$_cookieName]);
    }

    public static function getOptedValue()
    {
        if (!isset($_COOKIE[self::$_cookieName])) {
            return null;
        } else {
            $ret = $_COOKIE[self::$_cookieName];
            if ($ret != self::OPT_VALUE_IN && $ret != self::OPT_VALUE_OUT) {
                $exception = new Kwf_Exception('stored Cookie must be ' . self::OPT_VALUE_IN . ' or ' . self::OPT_VALUE_OUT);
                $exception->logOrThrow();
                return null;
            }
            return $ret;
        }
    }

    public static function setOptedValue($value)
    {
        if ($value != self::OPT_VALUE_IN && $value != self::OPT_VALUE_OUT) {
            throw new Kwf_Exception('$value must be ' . self::OPT_VALUE_IN . ' or ' . self::OPT_VALUE_OUT);
        }
        setcookie(self::$_cookieName, $value, time() + (3*365*24*60*60), '/');
    }
}

