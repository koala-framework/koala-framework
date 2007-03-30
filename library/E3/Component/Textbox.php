<?php
class E3_Component_Textbox extends E3_Component_Abstract
{
    private $_components;
    private $_content;
    private $_contentParts;

    private function _getContent()
    {
        if (!isset($this->_content)) {
            $rowset = $this->_dao->getTable('E3_Dao_Textbox')
                    ->find($this->getComponentId(), $this->getPageKey(), $this->getComponentKey());
            if ($rowset->count() == 1) {
                $this->_content = $rowset->current()->content;
            } else {
                $this->_content = '';
            }
        }
        return $this->_content;
    }

    private function _getContentParts($mode)
    {
        if (!isset($this->_contentParts))
        {
            $content = $this->_getContent();
            $this->_contentParts = array();
    
            $componentKey = $this->getComponentKey();
            if($componentKey!='') $componentKey .= ".";
    
            $componentNr = 0;
            $this->_components = array();
            while(preg_match('#^(.*?)\{([a-zA-Z0-9_]+)\}(.*)$#s', $content, $m)) {
                $this->_contentParts[] = array('type'=>'content', 'content'=>$m[1]);
                $className = $m[2];
                if (class_exists($className)) {
                    $componentNr++;
                    $component = new $className($this->_dao, $this->getComponentId(), '', $componentKey.$componentNr);
                    $this->_components[] = $component;
                    $this->_contentParts[] = array('type'=>'component', 'component'=>$component->getTemplateVars($mode));
                }
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
            $ret['content'] = $this->_getContent();
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

    public function getComponentInfo()
    {
      $info = parent::getComponentInfo();
      $this->_getContentParts('fe'); //um components zu laden
      foreach ($this->_components as $p) {
        $info += $p->getComponentInfo();
      }
      return $info;
    }
    public function saveFrontendEditing(Zend_Controller_Request_Http $request)
    {
        $rowset = $this->_dao->getTable('E3_Dao_Textbox')
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

        $this->_getContentParts('fe'); //um components zu laden
        $ret['createComponents'] = array();
        foreach ($this->_components as $p) {
            $ret['createComponents'] += $p->getComponentInfo();
        }
        return $ret;
    }
}
