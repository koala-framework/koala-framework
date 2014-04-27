<?php
class Kwf_Controller_Action_Cli_BuildController extends Kwf_Controller_Action_Cli_Abstract
{
    public static function getHelp()
    {
        return "build";
    }

    public static function getHelpOptions()
    {
        $types = array();
        foreach (Kwf_Util_Build::getInstance()->getTypes() as $t) {
            $types[] = $t->getTypeName();
        }
        return array(
            array(
                'param'=> 'type',
                'value'=> implode(',', $types),
                'valueOptional' => true,
                'help' => 'what to build'
            )
        );
    }

    public function indexAction()
    {
        $options = array(
            'types' => $this->_getParam('type'),
            'output' => true,
            'refresh' => true,
        );
        if (is_string($this->_getParam('exclude-type'))) {
            $options['excludeTypes'] = $this->_getParam('exclude-type');
        }
        Kwf_Util_Build::getInstance()->build($options);
        exit;
    }
}
