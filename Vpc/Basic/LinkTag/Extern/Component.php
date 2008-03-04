<?php
/**
 * @package Vpc
 * @subpackage Basic
 */
class Vpc_Basic_LinkTag_Extern_Component extends Vpc_Basic_LinkTag_Abstract_Component
{
    public static function getSettings()
    {
        $ret = array_merge(parent::getSettings(), array(
            'tablename'     => 'Vpc_Basic_LinkTag_Extern_Model',
            'componentName' => 'Link.Extern',
            'default'       => array(
                'target'        => 'http://',
                'rel'           => '',
                'param'         => '',
                'is_popup'      => false,
                'width'         => '0',
                'height'        => '0',
                'menubar'       => '1',
                'toolbar'       => '1',
                'locationbar'   => '1',
                'statusbar'     => '1',
                'scrollbars'    => '1',
                'resizeable'    => '1'
            )
        ));
        $ret['assets']['files'][] = 'vps/Vpc/Basic/LinkTag/Extern/Component.js';
        $ret['assets']['dep'][] = 'ExtCore';
        return $ret;
    }


    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $row = $this->_getRow();
        $ret['href'] = $row->target;
        if ($row->param) {
            $ret['href'] .= '?' . $row->param;
        }
        $ret['rel'] = '';
        if ($row->is_popup) {
            $ret['rel'] .= 'popup_'
                .($row->width ? 'width='.$row->width.',' : '')
                .($row->height ? 'height='.$row->height.',' : '')
                .'menubar='.($row->menubar ? 'yes' : 'no')
                .',toolbar='.($row->toolbar ? 'yes' : 'no')
                .',location='.($row->locationbar ? 'yes' : 'no')
                .',status='.($row->statusbar ? 'yes' : 'no')
                .',scrollbars='.($row->scrollbars ? 'yes' : 'no')
                .',resizable='.($row->resizeable ? 'yes' : 'no');
        }
        return $ret;
    }
}
