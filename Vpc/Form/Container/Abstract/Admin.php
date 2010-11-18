<?php
class Vpc_Form_Container_Abstract_Admin extends Vpc_Admin
{
    //TODO 1.10: kann dur editComponents ersetzt werden
    public function getExtConfig()
    {
        $ret = parent::getExtConfig();
        $parConfig = Vpc_Admin::getInstance(Vpc_Abstract::getChildComponentClass($this->_class, 'paragraphs'))
                ->getExtConfig();
        $parConfig['paragraphs']['componentIdSuffix'] = '-paragraphs';
        $ret = array_merge($ret, $parConfig);
        return $ret;
    }
}