<?php
/**
 * @package Vpc
 * @subpackage Basic
 */
class Vpc_Basic_Html_Index extends Vpc_Abstract
{
    private $_components = array();
    private $_content;
    private $_contentParts;
    protected $_settings = array(
        'width' => 400,
        'height' => 400,
        'content' => 'Lorem ipsum vix at error vocibus, sit at autem liber? Qui eu odio moderatius, populo pericula ex his. Mea hinc decore tempor ei, postulant honestatis eum ut. Eos te assum elaboraret, in ius fastidii officiis electram.'
    );
    protected $_tablename = 'Vpc_Basic_Html_IndexModel';
    const NAME = 'Standard.Html';

    private function _getContentParts()
    {
        if (!isset($this->_contentParts))
        {
            $content = $this->getSetting('content');
            $this->_contentParts = array();

            $componentNr = 0;
            $this->_components = array();
            while(preg_match('#^(.*?)\{([a-zA-Z0-9_]+)\}(.*)$#s', $content, $m)) {
                $this->_contentParts[] = $m[1];
                $className = $m[2];
                $componentNr++;
                try {
                    $component = $this->createComponent($className, $componentNr);
                } catch (Vpc_ComponentNotFoundException $e) {
                    $component = $this->createComponent('Vpc_Empty_Index', $componentNr);
                }
                $this->_components[] = $component;
                $this->_contentParts[] = array('component'=>$component->getTemplateVars());
                $content = $m[3];
            }
            if(!$m) $this->_contentParts[] = $content;
        }

        return $this->_contentParts;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['contentParts'] = $this->_getContentParts();
        $ret['template'] = 'Html.html';
        return $ret;
    }

    public function getChildComponents()
    {
        $this->_getContentParts(); //um components zu laden
        return $this->_components;
    }

}
