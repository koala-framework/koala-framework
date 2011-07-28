<?php
class Vps_Util_Check_Ip
{
    private static $_instances = array();

    public static function getInstance($ipCheck)
    {
        if (is_object($ipCheck)) return $ipCheck;
        if (!isset(self::$_instances[$ipCheck])) {
            self::$_instances[$ipCheck] = new $ipCheck();
        }
        return self::$_instances[$ipCheck];
    }

    private static function _getIpByDomain($domain)
    {
        $domainIp = gethostbyname($domain);
        if (preg_match('/^(\d){1,3}\.(\d){1,3}\.(\d){1,3}\.(\d){1,3}$/', $domainIp)) {
            return $domainIp;
        }
        return null;
    }

    /**
     * Liefert erlaubte IP Adressen / Domains
     *
     * Wildcards innerhalb einer IP-Adresse sind mit * möglich, z.B.: 192.168.0.*
     *
     * @return array $allowedAddresses Die erlaubten IP-Adressen und Domains
     */
    protected function _getAllowedAddresses()
    {
        return array(self::getVividPlanetIp());
    }

    /**
     * Liefert die IP von intern.vivid-planet.com zurück.
     *
     * Muss public bleiben, da es im Service verwendet wird um festzustellen, ob
     * wir mit Passwort 'test' einloggen dürfen. Weiters wird diese IP immer erlaubt.
     *
     * @return string $ipAddress Ip Adresse von intern.vivid-planet.com
     */
    public final static function getVividPlanetIp()
    {
        return self::_getIpByDomain('intern.vivid-planet.com');
    }

    /**
     * Checkt ob eine IP-Adresse in den von {@link _getAllowedAddresses} zurückgegebenen IPs / Domains erlaubt ist
     *
     * @param string $ip [optional] Die zu überprüfende IP-Adresse. Wenn nicht übergeben wird die REMOTE_ADDR verwendet
     * @param boolean $preventException [optional] Wenn true wird ein boolscher Wert returned und in keinem Fall eine Exception geworfen.
     * @return boolean $allowed Wenn zweites argument true ist wird returned ob die IP erlaubt ist oder nicht. Ansonste wird eine Exception geworfen.
     */
    public function checkIp($ip = null, $preventException = false)
    {
        if (is_null($ip)) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        if (!preg_match('/^(\d|\*){1,3}\.(\d|\*){1,3}\.(\d|\*){1,3}\.(\d|\*){1,3}$/', $ip)) {
            throw new Vps_Exception("The set IP address '$ip' is not an Ip address");
        }

        $allowedIps = $this->_getAllowedAddresses();
        if (!is_array($allowedIps)) {
            throw new Vps_Exception("_getAllowedAddresses() must return type 'array', '".gettype($allowedIps)."' given.");
        }

        // wenn domains, dann durch ips ersetzen
        foreach ($allowedIps as $key => $allowedIp) {
            $allowedIp = trim($allowedIp);
            $allowedIps[$key] = $allowedIp;
            if (!preg_match('/^(\d|\*){1,3}\.(\d|\*){1,3}\.(\d|\*){1,3}\.(\d|\*){1,3}$/', $allowedIp)) {
                $ipByDomain = self::_getIpByDomain($allowedIp);
                if ($ipByDomain) $allowedIps[$key] = $ipByDomain;
            }
        }

        $checkIpParts = explode('.', $ip);
        $ipCorrect = false;
        foreach ($allowedIps as $allowedIp) {
            $allowedIpParts = explode('.', $allowedIp);
            $ipCorrect = true;
            foreach ($allowedIpParts as $k => $allowedIpPart) {
                if ($allowedIpPart != '*' && $allowedIpPart != $checkIpParts[$k]) {
                    $ipCorrect = false;
                    break 1;
                }
            }
            if ($ipCorrect === true) break 1;
        }

        if ($ipCorrect === false) {
            if ($preventException) {
                return false;
            } else {
                throw new Vps_Util_Check_Ip_Exception("IP address '$ip' is not allowed.");
            }
        }
        return true;
    }
}
