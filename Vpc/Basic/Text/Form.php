<?php
class Vpc_Basic_Text_Form extends Vps_Auto_Vpc_Form
{
    public function __construct($class, $id = null)
    {
        parent::__construct($class, $id);
        $field = new Vps_Auto_Field_HtmlEditor('content', 'Content');
        $field->setData(new Vps_Auto_Data_Vpc_ComponentIds('content'));

        $ignoreSettings = array('tablename', 'componentName', 'childComponentClasses', 'default');
        foreach (call_user_func(array($class, 'getSettings')) as $key => $val) {
            if (!in_array($key, $ignoreSettings)) {
                $method = 'set' . ucfirst($key);
                $field->$method($val);
            }
        }
        $classes = Vpc_Abstract::getSetting($class, 'childComponentClasses');
        $field->setLinkComponentConfig(Vpc_Admin::getConfig($classes['link']));
        $field->setImageComponentConfig(Vpc_Admin::getConfig($classes['image']));
        $field->setDownloadComponentConfig(Vpc_Admin::getConfig($classes['download']));

        $field->setControllerUrl(Vpc_Admin::getInstance($class)->getControllerUrl());

        $this->fields->add($field);
    }
    
    public function prepareSave($parentRow, $postData)
    {
        $this->getRow()->content_edit = '';
        parent::prepareSave($parentRow, $postData);
    }

}
