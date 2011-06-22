<?php
//dieser controller ist notwendig damit cli scripte was mit dem apc cache machen können
//direkt in der cli ist das leider nicht möglich, da der speicher im webserver liegt
class Vps_Controller_Action_Util_ApcController extends Vps_Controller_Action
{
    public function preDispatch()
    {
        if (empty($_SERVER['PHP_AUTH_USER']) ||
            empty($_SERVER['PHP_AUTH_PW']) ||
            $_SERVER['PHP_AUTH_USER']!='vivid' ||
            $_SERVER['PHP_AUTH_PW']!='planet')
        {
            header('WWW-Authenticate: Basic realm="Testserver"');
            throw new Vps_Exception_AccessDenied();
        }
    }

    public function clearCacheAction()
    {
        if (class_exists('APCIterator')) {
            $prefix = Vps_Cache::getUniquePrefix();
            apc_delete_file(new APCIterator('user', '#^'.$prefix.'#'));
        } else {
            apc_clear_cache('user');
        }
        echo 'OK';
        exit;
    }

    public function getCounterValueAction()
    {
        $prefix = Vps_Cache::getUniquePrefix().'bench-';
        echo apc_fetch($prefix.$this->_getParam('name'));
        exit;
    }
}
