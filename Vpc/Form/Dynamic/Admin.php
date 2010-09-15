<?php
class Vpc_Form_Dynamic_Admin extends Vpc_Form_Admin
{
    //TODO 1.10: kann dur editComponents ersetzt werden
    public function getExtConfig()
    {
        $ret = Vpc_Abstract_Composite_Admin::getExtConfig();
        $parConfig = Vpc_Admin::getInstance(Vpc_Abstract::getChildComponentClass($this->_class, 'child', 'paragraphs'))
                ->getExtConfig();
        $parConfig['paragraphs']['componentIdSuffix'] = '-paragraphs';
        $ret = array_merge($ret, $parConfig);
        return $ret;
    }
}