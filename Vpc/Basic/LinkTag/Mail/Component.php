<?php
/**
 * @package Vpc
 * @subpackage Basic
 */
class Vpc_Basic_LinkTag_Mail_Component extends Vpc_Basic_LinkTag_Abstract_Component
{
    public static function getSettings()
    {
        return array_merge(parent::getSettings(), array(
            'tablename'     => 'Vpc_Basic_LinkTag_Mail_Model',
            'componentName' => 'Link.Mail'
        )); 
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['href'] = 'mailto:' . $this->_getRow()->mail;
        $ret['param'] = '';
        $ret['rel'] = '';
        return $ret;
    }
}
