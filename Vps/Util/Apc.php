<?php
//das ist notwendig damit cli scripte was mit dem apc cache machen können
//direkt in der cli ist das leider nicht möglich, da der speicher im webserver liegt
class Vps_Util_Apc
{
    public static function dispatchUtils()
    {
        if (empty($_SERVER['PHP_AUTH_USER']) ||
            empty($_SERVER['PHP_AUTH_PW']) ||
            $_SERVER['PHP_AUTH_USER']!='vivid' ||
            $_SERVER['PHP_AUTH_PW']!='planet')
        {
            header('WWW-Authenticate: Basic realm="APC Utils"');
            throw new Vps_Exception_AccessDenied();
        }

        if ($_SERVER['REQUEST_URI'] == '/vps/util/apc/clear-cache') {
            $s = microtime(true);
            if (class_exists('APCIterator')) {
                $prefix = Vps_Cache::getUniquePrefix();
                apc_delete_file(new APCIterator('user', '#^'.preg_quote($prefix).'#'));
            } else {
                apc_clear_cache('user');
            }
            echo 'OK '.round((microtime(true)-$s)*1000).' ms';
            exit;
        } else if ($_SERVER['REQUEST_URI'] == '/vps/util/apc/get-counter-value') {
            $prefix = Vps_Cache::getUniquePrefix().'bench-';
            echo apc_fetch($prefix.$this->_getParam('name'));
            exit;
        }
        throw new Vps_Exception_NotFound();
    }
}
