<?php
/**
 * @package Vpc
 * @subpackage Simple
 */
class Vpc_Simple_Textbox_Index extends Vpc_Abstract
{
    private $_components = array();
    private $_content;
    private $_contentParts;

    public static function getStaticSettings()
    {
        $config = array();
        $config['width'] = 400;
        $config['height'] = 400;
        $config['value'] = "test";
        return $config;
    }
    
    public function getContent()
    {
        if (!isset($this->_content)) {
            $rowset = $this->_dao->getTable('Vpc_Simple_Textbox_IndexModel')
                  ->find($this->getDbId(), $this->getComponentKey());
            if ($rowset->count() == 1) {
                $this->_content = $rowset->current()->content;
            } else {
                $this->_content = '';
            }
        }
        return $this->_content;
    }

    private function _getContentParts($mode = 'fe')
    {
        if (!isset($this->_contentParts))
        {
            $content = $this->getContent();
            $this->_contentParts = array();
    
            $componentNr = 0;
            $this->_components = array();
            while(preg_match('#^(.*?)\{([a-zA-Z0-9_]+)\}(.*)$#s', $content, $m)) {
                $this->_contentParts[] = array('type'=>'content', 'content'=>$m[1]);
                $className = $m[2];
                $componentNr++;
                try {
                    $component = $this->createComponent($className, $componentNr);
                } catch (Vpc_ComponentNotFoundException $e) {
                    $component = $this->createComponent('Vpc_Empty_Index', $componentNr);
                }
                $this->_components[] = $component;
                $this->_contentParts[] = array('type'=>'component', 'component'=>$component->getTemplateVars($mode));
                $content = $m[3];
            }
            if(!$m) $this->_contentParts[] = array('type'=>'content', 'content'=>$content);
        }

        return $this->_contentParts;
    }

    public function getTemplateVars($mode)
    {
        $ret = parent::getTemplateVars($mode);

        if ($mode == "edit") {
            $ret['content'] = $this->getContent();
        } else {
            $ret['contentParts'] = $this->_getContentParts($mode);
        }
        if ($mode == 'edit') {
            $ret['template'] = dirname(__FILE__).'/Textbox.html';
        } else {
             $ret['template'] = 'Textbox.html';
        }
        return $ret;
    }

    public function getChildComponents()
    {
        $this->_getContentParts('fe'); //um components zu laden
        return $this->_components;
    }

}
