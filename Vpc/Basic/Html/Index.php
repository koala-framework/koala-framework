<?php
/**
 * @package Vpc
 * @subpackage Basic
 */
class Vpc_Basic_Html_Index extends Vpc_Abstract
{
    private $_contentParts;
    protected $_settings = array(
        'width' => 400,
        'height' => 400,
        'content' => 'Lorem ipsum vix at error vocibus, sit at autem liber? Qui eu odio moderatius, populo pericula ex his. Mea hinc decore tempor ei, postulant honestatis eum ut. Eos te assum elaboraret, in ius fastidii officiis electram.'
    );
    protected $_tablename = 'Vpc_Basic_Html_IndexModel';
    const NAME = 'Standard.Html';
    protected $_components = array();

    protected function _parseContentParts($content)
    {
        $ret = array();
        $componentNr = 0;
        while(preg_match('#^(.*?)\{([a-zA-Z0-9_]+)\}(.*)$#s', $content, $m)) {
            $ret[] = $m[1];
            $className = $m[2];
            $componentNr++; //todo: wie nr ermitteln?? verträgt sich das mit rte bildern?
            try {
                $component = $this->createComponent($className, $componentNr);
            } catch (Vpc_ComponentNotFoundException $e) {
                $component = $this->createComponent('Vpc_Empty_Index', $componentNr);
            }
            $this->_components[$componentNr] = $component;
            $ret[] = $component;
            $content = $m[3];
        }

        if(!$m) $ret[] = $content;
        return $ret;
    }

    private function _getContentParts($content)
    {
        $this->_contentParts = $this->_parseContentParts($content);
        return $this->_contentParts;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['contentParts'] = array();
        foreach ($this->_getContentParts($this->getSetting('content')) as $part) {
            if (is_string($part)) {
                $ret['contentParts'][] = $part;
            } else {
                $ret['contentParts'][] = $part->getTemplateVars();
            }
        }
        $ret['template'] = 'Html.html';
        return $ret;
    }

    protected function _getEditContent()
    {
        return $this->getSetting('content');
    }

    public function getChildComponents()
    {
        $ret = array();
        $content = $this->_getEditContent();
        foreach ($this->_getContentParts($content) as $part) {
            if (!is_string($part)) {
                $ret[] = $part;
            }
        }
        return $ret;
    }
}
