<?php
/**
 * @package Vpc
 * @subpackage Basic
 */
class Vpc_Basic_Link_Intern_Component extends Vpc_Abstract
{
    protected $_settings = array();
    protected $_tablename = 'Vpc_Basic_Link_Intern_Model';
    const NAME = 'Standard.Link';

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['template'] = 'Basic/Link.html';
        return $ret;
    }
}
