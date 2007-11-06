<?php
/**
 * @package Vpc
 * @subpackage Basic
 */
class Vpc_Basic_Link_Mail_Component extends Vpc_Basic_Link_Component
{
    protected $_settings = array(
        'hasLinktext'   => true,
        'text'          => 'Linktext',
        'target'        => ''
    );
    protected $_tablename = 'Vpc_Basic_Link_Mail_Model';
    const NAME = 'Standard.Link.Mail';

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret += $this->getSettings();
        $ret['template'] = 'Basic/Link.html';
        $target = $this->getSetting('target');
        $page = $this->getPageCollection()->findPage($target);
        $ret['href'] = $this->getPageCollection()->getUrl($page);
        return $ret;
    }
}
