<?php
class Vps_Controller_Action_Cli_Abstract extends Vps_Controller_Action
{
    public function preDispatch()
    {
        parent::preDispatch();

        //php sux
        $options = call_user_func(array(get_class($this), 'getHelpOptions'));

        foreach ($options as $opt) {
            $p = $this->_getParam($opt['param']);
            if (isset($opt['value']) && ($p===true || !$p) &&
                    !(isset($opt['valueOptional']) && $opt['valueOptional'])) {
                throw new Vps_ClientException("Parameter '$opt[param]' is missing");
            }
            if (is_null($p) && isset($opt['value'])) {
                if (is_array($opt['value'])) {
                    $v = $opt['value'][0];
                } else {
                    $v = $opt['value'];
                }
                $this->getRequest()->setParam($opt['param'], $v);
                $p = $v;
            }
            if (isset($opt['value']) && is_array($opt['value']) && !in_array($p, $opt['value'])) {
                throw new Vps_ClientException("Invalid value for parameter '$opt[param]'");
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
        if ($ret != 0) throw new Vps_ClientException("Aktion fehlgeschlagen");
    }

    protected static function _getConfigSections()
    {
        $webConfigFull = new Zend_Config_Ini('application/config.ini', null);
        $sections = array();
        $processedServers = array();
        foreach ($webConfigFull as $k=>$i) {
            if ($i->server) {
                $s = $i->server->host.':'.$i->server->dir;
                if ($i->server->host != 'vivid' && !in_array($s, $processedServers)) {
                    $sections[] = $k;
                    $processedServers[] = $s;
                }
            }
        }
        return $sections;
    }
}
