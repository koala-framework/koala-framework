<?php
class Vpc_Basic_Flash_Upload_Admin extends Vpc_Abstract_Admin
{
    public function componentToString(Vps_Component_Data $data)
    {
        $row = $data->getComponent()->getRow();
        $fRow = $row->getParentRow(Vpc_Abstract::getSetting($data->componentClass, 'uploadModelRule'));
        if (!$fRow) {
            return str_replace('.', ' ', Vps_Trl::getInstance()->trlStaticExecute(Vpc_Abstract::getSetting($data->componentClass, 'componentName')));
        }
        return $fRow->filename.'.'.$fRow->extension;
    }
}