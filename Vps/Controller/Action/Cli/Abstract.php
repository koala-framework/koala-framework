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
            if (is_array($opt['value']) && !in_array($p, $opt['value'])) {
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
}
