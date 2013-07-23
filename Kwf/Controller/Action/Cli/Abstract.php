<?php
class Kwf_Controller_Action_Cli_Abstract extends Kwf_Controller_Action
{
    public function preDispatch()
    {
        parent::preDispatch();

        set_time_limit(0);

        Zend_Registry::get('config')->debug->error->log = false;

        $help = call_user_func(array(get_class($this), 'getHelp'));
        if (!$help) throw new Kwf_ClientException("This command is not avaliable");

        //php sux
        $options = call_user_func(array(get_class($this), 'getHelpOptions'));

        if ($this->getRequest()->getActionName() == 'index') { //helpOptions are only valid vor index action atm
            foreach ($options as $opt) {
                $p = $this->_getParam($opt['param']);
                if (isset($opt['value']) && ($p===true || !$p) &&
                        !(isset($opt['valueOptional']) && $opt['valueOptional']) &&
                        !(isset($opt['allowBlank']) && $opt['allowBlank'])) {
                    throw new Kwf_ClientException("Parameter '$opt[param]' is missing");
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
    //                 throw new Kwf_ClientException("Invalid value for parameter '$opt[param]'");
                }
            }
        }

        $this->_helper->viewRenderer->setNoRender();
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
        passthru($cmd, $ret);
        if ($ret != 0) throw new Kwf_ClientException("Command failed");
    }

    protected static function _getConfigSections()
    {
        $configClass = get_class(Kwf_Registry::get('config'));
        $configFull = new Zend_Config_Ini('config.ini', null);
        $sections = array();
        $processedServers = array();
        foreach ($configFull as $k=>$i) {
            if ($k == 'dependencies') continue;
            $config = Kwf_Config_Web::getInstance($k);
            if ($config->server && $config->server->host) {
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
        $webConfigFull = new Zend_Config_Ini('config.ini', null);
        $sections = array();
        $processedDomains = array();
        foreach ($webConfigFull as $k=>$i) {
            if ($k == 'dependencies') continue;
            $config = Kwf_Config_Web::getInstance($k);
            if ($config->server && $config->server->domain) {
                if ( !in_array($config->server->domain, $processedDomains)) {
                    $sections[] = $k;
                    $processedDomains[] = $config->server->domain;
                }
            }
        }
        $sections = array_reverse($sections);
        $currentSection = Kwf_Setup::getConfigSection();
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

    protected static function _getConfigSectionsWithHost()
    {
        $webConfigFull = new Zend_Config_Ini('config.ini', null);
        $sections = array();
        $processedDomains = array();
        foreach ($webConfigFull as $k=>$i) {
            if ($k == 'dependencies') continue;
            $config = Kwf_Config_Web::getInstance($k);
            if ($config->server && $config->server->host) {
                $sections[] = $k;
            }
        }
        return $sections;
    }
}
