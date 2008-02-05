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
        if ($classes['link']) {
            $c = Vpc_Admin::getInstance($classes['link'])->getExtConfig();
            $field->setLinkComponentConfig($c);
        }
        if ($classes['image']) {
            $c = Vpc_Admin::getInstance($classes['image'])->getExtConfig();
            $field->setImageComponentConfig($c);
        }
        if ($classes['download']) {
            $c = Vpc_Admin::getInstance($classes['download'])->getExtConfig();
            $field->setDownloadComponentConfig($c);
        }

        $field->setControllerUrl(Vpc_Admin::getInstance($class)->getControllerUrl());

        $v = Zend_Registry::get('config')->application->version;
        $field->setCssFile('/assets/AllFrontend.css?v='.$v);

        $this->fields->add($field);
    }

    public function prepareSave($parentRow, $postData)
    {
        $this->getRow()->content_edit = null;
        parent::prepareSave($parentRow, $postData);
    }

    public function setHtmlEditorLabel($title)
    {
        $this->fields[0]->setFieldLabel($title);
        return $this;
    }

}
