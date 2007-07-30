<?php
class Vpc_Formular_HiddenField_Setup extends Vpc_Setup_Abstract
{
    public function setup()
    {
    	$fields = array();
        $this->createTable('component_formular_hiddenfield', $fields);
    }
}