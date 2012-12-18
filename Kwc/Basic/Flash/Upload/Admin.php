<?php
class Kwc_Basic_Flash_Upload_Admin extends Kwc_Abstract_Admin
{
    public function componentToString(Kwf_Component_Data $data)
    {
        $row = $data->getComponent()->getRow();
        $fRow = $row->getParentRow(Kwc_Abstract::getSetting($data->componentClass, 'uploadModelRule'));
        if (!$fRow) {
            return str_replace('.', ' ', Kwf_Trl::getInstance()->trlStaticExecute(Kwc_Abstract::getSetting($data->componentClass, 'componentName')));
        }
        return $fRow->filename.'.'.$fRow->extension;
    }
}