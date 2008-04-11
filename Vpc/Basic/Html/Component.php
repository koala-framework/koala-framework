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
            'componentName' => 'Html',
            'componentIcon' => new Vps_Asset('tag'),
            'tablename'     => 'Vpc_Basic_Html_Model',
            'width'         => 400,
            'height'        => 400,
            'default'       => array(
                'content' => Vpc_Abstract::LOREM_IPSUM
            )
        ));
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['content'] = $this->_getRow()->content;
        return $ret;
    }

    public function getSearchVars()
    {
        $ret = parent::getSearchVars();
        $ret['text'] .= ' '.strip_tags($this->_getRow()->content);
        return $ret;
    }
}
