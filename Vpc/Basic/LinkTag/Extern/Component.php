<?php
/**
 * @package Vpc
 * @subpackage Basic
 */
class Vpc_Basic_LinkTag_Extern_Component extends Vpc_Basic_LinkTag_Abstract_Component
{
    public static function getSettings()
    {
        return array_merge(parent::getSettings(), array(
            'tablename'     => 'Vpc_Basic_Link_Extern_Model',
            'componentName' => 'Link.Extern',
            'default'       => array(
                'target'        => 'http://',
                'rel'           => '',
                'param'         => '',
                'is_popup'      => false,
                'width'         => '400',
                'height'        => '400',
                'menubar'       => '0',
                'toolbar'       => '0',
                'locationbar'   => '0',
                'statusbar'     => '0',
                'scrollbars'    => '0',
                'resizeable'    => '0'
            )
        )); 
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['href'] = $this->_getRow()->target;
        $ret['param'] = $this->_getRow()->param;
        $ret['rel'] = $this->_getRow()->rel;
        return $ret;
    }
}
