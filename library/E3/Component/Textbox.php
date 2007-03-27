<?php
class E3_Component_Textbox extends E3_Component_Abstract
{
    private $_components;
    public function getTemplateVars()
    {
        $rowset = $this->_dao->getTable('E3_Dao_Textbox')
                ->find($this->getComponentId(), $this->getPageKey(), $this->getComponentKey());
        if ($rowset->count() == 1) {
        	$content = $rowset->current()->content;
        } else {
        	$content = '';
        }
        $contentParts = array();

        $componentKey = $this->getComponentKey();
        if($componentKey!='') $componentKey .= ".";

        $componentNr = 0;
        $this->_components = array();
        while(preg_match('#^(.*?)\{([a-zA-Z0-9_]+)\}(.*)$#', $content, $m)) {
            $contentParts[] = array('type'=>'content', 'content'=>$m[1]);
            $className = $m[2];
            if (class_exists($className)) {
                $componentNr++;
                $component = new $className($this->_dao, $this->getComponentId(), '', $componentKey.$componentNr);
                $this->_components[] = $component;
                $contentParts[] = array('type'=>'component', 'component'=>$component->getTemplateVars());
            }
            $content = $m[3];
        }
        if(!$m) $contentParts[] = array('type'=>'content', 'content'=>$content);
        
        $ret = parent::getTemplateVars();
        $ret['contentParts'] = $contentParts;
       	$ret['template'] = 'Textbox.html';

        return $ret;
    }

    public function getComponentInfo()
    {
    	$info = parent::getComponentInfo();
    	foreach ($this->_components as $p) {
    		$info += $p->getComponentInfo();
    	}
    	return $info;
    }
    public function saveFrontendEditing()
    {
        $rowset = $this->_dao->getTable('E3_Dao_Textbox')
                ->find($this->getComponentId(), $this->getPageKey(), $this->getComponentKey());
        if ($rowset->count() == 1) {
        	$row = $rowset->current();
        } else {
        	//todo: throw error
        }
        if (isset($_POST['content'])) {
            $row->content = $_POST['content'];
        }
        $row->save();
    }
}
