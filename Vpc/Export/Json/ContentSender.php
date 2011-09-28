<?php
class Vpc_Export_Json_ContentSender extends Vps_Component_Abstract_ContentSender_Default
{
    public function sendContent()
    {
        header('Content-type: application/json; charset: utf-8');
        echo Zend_Json::encode($this->_data->parent->getComponent()->getExportData());
    }
}
