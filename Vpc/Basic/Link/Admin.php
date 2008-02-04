<?php
class Vpc_Basic_Link_Admin extends Vpc_Admin
{
    public function getControllerClass()
    {
        //HACK
        //TODO: Ein Form-Feld mit ComboBox + CardLayout
        return 'Vpc.Basic.LinkTag.Panel';
    }

    public function setup()
    {
        $classes = Vpc_Abstract::getSetting($this->_class, 'childComponentClasses');
        Vpc_Admin::getInstance($classes['linkTag'])->setup();

        $fields['text'] = 'text';
        $this->createFormTable('vpc_basic_link', $fields);
    }
}
