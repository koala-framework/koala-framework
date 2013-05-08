<?php
class Kwf_Controller_Action_Cli_ClearCacheController extends Kwf_Controller_Action_Cli_Abstract
{
    public static function getHelp()
    {
        return "clears all caches";
    }

    public function indexAction()
    {
        $options = array();
        if ($this->_getParam('skip-other-servers')) {
            $options['skipOtherServers'] = true;
        }
        Kwf_Util_ClearCache::getInstance()->clearCache($this->_getParam('type'), true, true, $options);
        exit;
    }

    public function writeMaintenanceAction()
    {
        Kwf_Util_Maintenance::writeMaintenanceBootstrapSelf();
        exit;
    }

    public function restoreMaintenanceAction()
    {
        Kwf_Util_Maintenance::restoreMaintenanceBootstrapSelf();
        exit;
    }

    public static function getHelpOptions()
    {
        $types = array();
        foreach (Kwf_Util_ClearCache::getInstance()->getTypes() as $t) {
            $types[] = $t->getTypeName();
        }
        return array(
            array(
                'param'=> 'type',
                'value'=> implode(',', $types),
                'valueOptional' => true,
                'help' => 'what to clear'
            )
        );
    }
}
