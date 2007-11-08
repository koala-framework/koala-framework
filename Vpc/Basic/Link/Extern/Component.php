<?php
/**
 * @package Vpc
 * @subpackage Basic
 */
class Vpc_Basic_Link_Extern_Component extends Vpc_Basic_Link_Component
{
    public static function getSettings()
    {
        return array_merge(parent::getSettings(), array(
            'tablename'     => 'Vpc_Basic_Link_Extern_Model',
            'componentName' => 'Standard.Link.Extern',
            'hasLinktext'   => true,
            'default'       => array(
                'text'          => 'Linktext',
                'target'        => 'http://',
                'is_popup'      => false,
                'width'         => '400',
                'height'        => '400'
            )
        )); 
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['hasLinktext'] = $this->_getSetting('hasLinktext');
        $ret['href'] = $this->_row->target;
        $ret['param'] = $this->_row->param;
        $ret['rel'] = $this->_row->rel;
        $ret['text'] = $this->_row->text;
        return $ret;
    }
}
