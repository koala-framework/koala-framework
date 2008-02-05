<?php
class Vpc_Basic_Link_Admin extends Vpc_Abstract_Composite_Admin
{
    public function getExtConfig()
    {
        //HACK
        //TODO: Ein Form-Feld mit ComboBox + CardLayout
        return array_merge(parent::getExtConfig(), array(
            'xtype'=>'vpc.linktag'
        ));
    }

    public function setup()
    {
        parent::setup();
        $classes = Vpc_Abstract::getSetting($this->_class, 'childComponentClasses');
        Vpc_Admin::getInstance($classes['linkTag'])->setup();

        $fields['text'] = 'text';
        $this->createFormTable('vpc_basic_link', $fields);
    }
}
