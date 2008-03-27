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
        $r = $this->_getRow();
        $p = array();
        if ($r->subject) {
            $p[] = 'subject=' . $r->subject;
        }
        if ($r->text) {
            $p[] = 'body=' . $r->text;
        }
        $ret['href'] = 'mailto:' . $r->mail;
        if ($p) {
            $ret['href'] .= '?' . implode('&', $p);
        }
        $ret['rel'] = '';
        return $ret;
    }
}
