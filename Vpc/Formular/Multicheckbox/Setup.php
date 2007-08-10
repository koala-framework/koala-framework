<?php
class Vpc_Formular_Multicheckbox_Setup extends Vpc_Setup_Abstract
{
    public function setup()
    {
        $fields['horizontal'] = 'tinyint(4) NOT NULL';
        $this->createTable('component_formular_multicheckbox', $fields);
    }
}