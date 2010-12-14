<?php
//dieser controller ist notwendig damit cli scripte was mit dem apc cache machen können
//direkt in der cli ist das leider nicht möglich, da der speicher im webserver liegt
class Vps_Controller_Action_Util_ApcController extends Vps_Controller_Action
{
    public function clearCacheAction()
    {
        //darf nur von cli aus aufgerufen werden
        if ($_SERVER['SERVER_ADDR']!=$_SERVER['REMOTE_ADDR']) {
            throw new Vps_Exception_AccessDenied();
        }

        if (class_exists('APCIterator')) {
            $prefix = Vps_Cache::getUniquePrefix();
            $i = new APCIterator('user', '#^'.$prefix.'#');
            foreach ($i as $entry) {
                if (substr($entry['key'], 0, strlen($prefix.'bench-')) != $prefix.'bench-') {
                    apc_delete($entry['key']);
                }
            }
        } else {
            apc_clear_cache('user');
        }
        echo 'OK';
        exit;
    }

    public function getCounterValueAction()
    {
        //darf nur von cli aus aufgerufen werden
        if ($_SERVER['SERVER_ADDR']!=$_SERVER['REMOTE_ADDR']) {
            throw new Vps_Exception_AccessDenied();
        }

        $prefix = Vps_Cache::getUniquePrefix().'bench-';
        echo apc_fetch($prefix.$this->_getParam('name'));
        exit;
    }
}
