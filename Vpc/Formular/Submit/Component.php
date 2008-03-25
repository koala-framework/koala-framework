<?php
class Vpc_Formular_Submit_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        return array_merge(parent::getSettings(), array(
            'componentName' => 'Formular Fields.Submit',
            'tablename' => 'Vpc_Formular_Submit_Model',
            'default' => array(
                'text' => trlVps('Submit'),
                'width' => '150'
            )
        ));
    }

    function getTemplateVars()
    {
        $return = parent::getTemplateVars();
        $return['text'] = $this->_getRow()->text;
        $return['width'] = $this->_getRow()->width;
        $return['name'] = $this->getStore('name');
        return $return;
    }
}
