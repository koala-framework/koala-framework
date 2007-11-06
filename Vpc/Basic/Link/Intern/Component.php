<?php
/**
 * @package Vpc
 * @subpackage Basic
 */
class Vpc_Basic_Link_Intern_Component extends Vpc_Basic_Link_Component
{
    protected $_settings = array(
        'hasLinktext'  => true,
        'text'          => 'Linktext',
    );
    protected $_tablename = 'Vpc_Basic_Link_Intern_Model';
    const NAME = 'Standard.Link.Intern';

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
