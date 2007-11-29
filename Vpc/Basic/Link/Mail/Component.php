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
            'componentName' => 'Standard.Link.Mail'
        )); 
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['href'] = 'mailto:' . $this->_row->mail;
        $ret['param'] = '';
        $ret['rel'] = '';
        return $ret;
    }
}
