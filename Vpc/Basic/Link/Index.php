<?php
/**
 * @package Vpc
 * @subpackage Basic
 */
class Vpc_Basic_Link_Index extends Vpc_Abstract
{
    protected $_settings = array();
    protected $_tablename = 'Vpc_Basic_Link_IndexModel';
    const NAME = 'Standard.Link';

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['template'] = 'Basic/Link.html';
        return $ret;
    }
}
