<?php
class Vpc_Formular_Submit_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        return array_merge(parent::getSettings(), array(
            'componentName' => 'Formular Fields.Submit',
            'tablename' => 'Vpc_Formular_Submit_Model',
            'default' => array(
                'name' => 'submit',
                'text' => 'Submit'
            )
        ));
    }

    function getTemplateVars()
    {
        $return = parent::getTemplateVars();
        $return['text'] = $this->_getRow()->text;
        if (isset($this->_getRow()->name)) {
            $return['name'] = $this->_getRow()->name;
        } else {
            $return['name'] = $this->_store['name'];
        }
        return $return;
    }
}
