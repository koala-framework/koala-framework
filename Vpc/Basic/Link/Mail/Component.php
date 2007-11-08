<?php
/**
 * @package Vpc
 * @subpackage Basic
 */
class Vpc_Basic_Link_Mail_Component extends Vpc_Basic_Link_Component
{
    public static function getSettings()
    {
        return array_merge(parent::getSettings(), array(
            'tablename'     => 'Vpc_Basic_Link_Mail_Model',
            'componentName' => 'Standard.Link.Mail',
            'hasLinktext'  => true,
            'default'       => array(
                'text'          => 'Linktext'
            )
        )); 
    }
    
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['hasLinktext'] = $this->_getSetting('hasLinktext');
        $ret['href'] = 'mailto: ' . $this->_row->target;
        $ret['param'] = '';
        $ret['rel'] = '';
        $ret['text'] = $this->_row->text;
        return $ret;
    }
}
