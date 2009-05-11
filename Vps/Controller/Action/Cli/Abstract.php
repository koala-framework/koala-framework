<?php
class Vps_Controller_Action_Cli_Abstract extends Vps_Controller_Action
{
    public function preDispatch()
    {
        parent::preDispatch();

        set_time_limit(0);

        Zend_Registry::get('config')->debug->error->log = false;

        $help = call_user_func(array(get_class($this), 'getHelp'));
        if (!$help) throw new Vps_ClientException("This command is not avaliable");

        //php sux
        $options = call_user_func(array(get_class($this), 'getHelpOptions'));

        foreach ($options as $opt) {
            $p = $this->_getParam($opt['param']);
            if (isset($opt['value']) && ($p===true || !$p) &&
                    !(isset($opt['valueOptional']) && $opt['valueOptional']) &&
                    !(isset($opt['allowBlank']) && $opt['allowBlank'])) {
                throw new Vps_ClientException("Parameter '$opt[param]' is missing");
            }
            if (is_null($p) && isset($opt['value']) && !(isset($opt['allowBlank']) && $opt['allowBlank'])) {
                if (is_array($opt['value'])) {
                    $v = $opt['value'][0];
                } else {
                    $v = $opt['value'];
                }
                $this->getRequest()->setParam($opt['param'], $v);
                $p = $v;
            }
            if (isset($opt['value']) && is_array($opt['value']) && !in_array($p, $opt['value']) && !(isset($opt['allowBlank']) && $opt['allowBlank'])) {
//                 throw new Vps_ClientException("Invalid value for parameter '$opt[param]'");
            }
        }
    }

    public static function getHelp()
    {
        return '';
    }

    public static function getHelpOptions()
    {
        return array();
    }

    protected function _systemCheckRet($cmd)
    {
        $ret = null;
        system($cmd, $ret);
        if ($ret != 0) throw new Vps_ClientException("Command failed");
    }

    protected static function _getConfigSections()
    {
        $configClass = get_class(Vps_Registry::get('config'));
        $configFull = new Zend_Config_Ini(VPS_PATH.'/config.ini', null);
        $sections = array();
        $processedServers = array();
        foreach ($configFull as $k=>$i) {
            if ($k == 'dependencies') continue;
            $config = Vps_Config_Web::getInstance($k);
            if ($config->server) {
                $s = $config->server->host.':'.$config->server->dir;
                if (/*$i->server->host != 'vivid' &&*/ !in_array($s, $processedServers)) {
                    $sections[] = $k;
                    $processedServers[] = $s;
                }
            }
        }
        return $sections;
    }
    protected static function _getConfigSectionsWithTestDomain()
    {
        $webConfigFull = new Zend_Config_Ini('application/config.ini', null);
        $sections = array();
        $processedDomains = array();
        foreach ($webConfigFull as $k=>$i) {
            $config = Vps_Config_Web::getInstance($k);
            if ($config->server && $config->server->domain) {
                if ( !in_array($config->server->domain, $processedDomains)) {
                    $sections[] = $k;
                    $processedDomains[] = $config->server->domain;
                }
            }
        }
        $sections = array_reverse($sections);
        $currentSection = Vps_Setup::getConfigSection();
        $ret = array();
        foreach ($sections as $i) {
            if ($i == $currentSection) {
                array_unshift($ret, $i);
            } else {
                $ret[] = $i;
            }
        }
        return $ret;
    }
}
