<?php
class Vpc_Basic_Link_Admin extends Vpc_Abstract_Composite_Admin
{
    public function getExtConfig()
    {
        //HACK
        //TODO: Ein Form-Feld mit ComboBox + CardLayout
        return array_merge(parent::getExtConfig(), array(
            'xtype'=>'vps.autoform'
        ));
    }

    public function setup()
    {
        parent::setup();
        $fields['text'] = 'text';
        $this->createFormTable('vpc_basic_link', $fields);
    }
}
