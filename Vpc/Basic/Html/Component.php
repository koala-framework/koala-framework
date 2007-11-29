<?php
/**
 * @package Vpc
 * @subpackage Basic
 */
class Vpc_Basic_Html_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        return array_merge(parent::getSettings(), array(
            'componentName' => 'Standard.Html',
            'tablename' => 'Vpc_Basic_Html_Model',
            'width' => 400,
            'height' => 400,
            'default' => array(
                'content' => Vpc_Abstract::LOREM_IPSUM
            )
        ));
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['content'] = $this->_row->content;
        return $ret;
    }
}
