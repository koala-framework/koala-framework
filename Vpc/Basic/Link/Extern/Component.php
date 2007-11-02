<?php
/**
 * @package Vpc
 * @subpackage Basic
 */
class Vpc_Basic_Link_Extern_Component extends Vpc_Abstract
{
    protected $_settings = array(
        'hasLinktext'   => true,
        'text'          => 'Linktext',
        'target'        => 'http://',
        'width'         => '400',
        'height'        => '400'
    );
    protected $_tablename = 'Vpc_Basic_Link_Extern_Model';
    const NAME = 'Standard.Link.Extern';

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
