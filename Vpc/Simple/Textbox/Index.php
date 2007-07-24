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
            $rowset = $this->_dao->getTable('Vps_Dao_Textbox')
                    ->find($this->getComponentId(), $this->getPageKey(), $this->getComponentKey());
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
    
            $componentKey = $this->getComponentKey();
            if($componentKey!='') $componentKey .= ".";
    
            $componentNr = 0;
            $this->_components = array();
            while(preg_match('#^(.*?)\{([a-zA-Z0-9_]+)\}(.*)$#s', $content, $m)) {
                $this->_contentParts[] = array('type'=>'content', 'content'=>$m[1]);
                $className = $m[2];
                $componentNr++;
                try {
                    $component = $this->createComponent($className, 0, $componentNr);
                } catch (Vpc_ComponentNotFoundException $e) {
                    $component = $this->createComponent('Vpc_Empty_Index', 0, $componentNr);
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
        $ret['id'] = $this->getComponentId(); //test
        return $ret;
    }

    public function getComponentInfo()
    {
      $info = parent::getComponentInfo();
      $this->_getContentParts('fe'); //um components zu laden
      foreach ($this->_components as $p) {
        $info += $p->getComponentInfo();
      }
      return $info;
    }
    
    public function getChildComponents()
    {
        $this->_getContentParts('fe'); //um components zu laden
        return $this->_components;
    }

    public function saveContent($content) {
        $table = $this->_dao->getTable('Vps_Dao_Textbox');
        $rowset = $table->find($this->getComponentId(), $this->getPageKey(), $this->getComponentKey());
        if ($rowset->count() == 1) {
            $row = $rowset->current();
        } else {
            $row = $table->createRow();
            $row->component_id = $this->getComponentId();
            $row->page_key = $this->getPageKey();
            $row->component_key = $this->getComponentKey();
        }
        
        $row->content = $content;
        $result = $row->save();
        return is_array($result);
    }
    
    public function saveFrontendEditing(Zend_Controller_Request_Http $request)
    {
        $rowset = $this->_dao->getTable('Vps_Dao_Textbox')
                ->find($this->getComponentId(), $this->getPageKey(), $this->getComponentKey());
        if ($rowset->count() == 1) {
            $row = $rowset->current();
        } else {
            //todo: throw error
        }
        $content = $request->getPost('content');
        if (!is_null($content)) {
            $row->content = $content;
            $row->save();
        }

        $ret = parent::saveFrontendEditing($request);
/*
        $this->_getContentParts('fe'); //um components zu laden
        $ret['createComponents'] = array();
        foreach ($this->_components as $p) {
            $ret['createComponents'] += $p->getComponentInfo();
        }*/
        return $ret;
    }
    
    public function getFrontendEditingData()
    {
        $data = array();
        $data['data'][] = array('id' => 'content', 'value' => $this->getContent());
        return $data;
    }

}
