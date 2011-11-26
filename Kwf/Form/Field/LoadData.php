<?php
/**
 * @ingroup form
 */
class Kwf_Form_Field_LoadData extends Kwf_Form_Field_SimpleAbstract
{
    public function getMetaData($model)
    {
        return null;
    }

    public function prepareSave(Kwf_Model_Row_Interface $row, $postData)
    {
        Kwf_Form_Field_Abstract::prepareSave($row, $postData);
    }
}
