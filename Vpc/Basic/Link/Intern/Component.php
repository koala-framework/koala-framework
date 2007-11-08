<?php
/**
 * @package Vpc
 * @subpackage Basic
 */
class Vpc_Basic_Link_Intern_Component extends Vpc_Basic_Link_Component
{
    public static function getSettings()
    {
        return array_merge(parent::getSettings(), array(
            'tablename'     => 'Vpc_Basic_Link_Intern_Model',
            'componentName' => 'Standard.Link.Intern',
            'hasLinktext'  => true,
            'default'       => array(
                'text'          => 'Linktext'
            )
        )); 
    }
    
    public function getTemplateVars()
    {
        $target = $this->_row->target;
        $page = $this->getPageCollection()->findPage($target);
        if ($page) {
            $href = $this->getPageCollection()->getUrl($page);
        } else {
            $href = $target;
        }
        
        $ret = parent::getTemplateVars();
        $ret['hasLinktext'] = $this->_getSetting('hasLinktext');
        $ret['href'] = $href;
        $ret['param'] = $this->_row->param;
        $ret['rel'] = $this->_row->rel;
        $ret['text'] = $this->_row->text;
        return $ret;
    }
}
