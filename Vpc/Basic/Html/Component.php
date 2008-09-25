<?php
/**
 * @package Vpc
 * @subpackage Basic
 */
class Vpc_Basic_Html_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = array_merge(parent::getSettings(), array(
            'componentName' => trlVps('Html'),
            'componentIcon' => new Vps_Asset('tag'),
            'tablename'     => 'Vpc_Basic_Html_Model',
            'width'         => 400,
            'height'        => 400,
            'default'       => array(
                'content' => Vpc_Abstract::LOREM_IPSUM
            )
        ));
        $ret['flags']['searchContent'] = true;
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['content'] = $this->_getRow()->content;
        return $ret;
    }

    public function hasContent()
    {
        if (trim($this->_getRow()->content) != "") {
            return true;
        }
        return false;
    }

    public function getSearchContent()
    {
        return strip_tags($this->_getRow()->content);
    }
}
