<?php
class Vpc_Basic_Text_Admin extends Vpc_Admin
{
    public function setup()
    {
        //TODO: kann das ned einfach für alle unterkomponenten gemacht werden?!
        $generators = Vpc_Abstract::getSetting($this->_class, 'generators');
        if ($generators['child']['component']['link']) {
            Vpc_Admin::getInstance($generators['child']['component']['link'])->setup();
        }
        if ($generators['child']['component']['image']) {
            Vpc_Admin::getInstance($generators['child']['component']['image'])->setup();
        }
        if ($generators['child']['component']['download']) {
            Vpc_Admin::getInstance($generators['child']['component']['download'])->setup();
        }

        $fields['content'] = 'text NOT NULL';
        $this->createFormTable('vpc_basic_text', $fields);
    }
}
