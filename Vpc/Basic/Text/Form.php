<?php
class Vpc_Basic_Text_Form extends Vpc_Abstract_Form
{
    public function __construct($name, $class, $id = null)
    {
        parent::__construct($name, $class, $id);
        $field = new Vps_Form_Field_HtmlEditor('content', 'Content');
        $field->setData(new Vps_Data_Vpc_ComponentIds('content'));
        $field->setFieldLabel(trlVps('Text'));

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

    public function setHtmlEditorLabel($title)
    {
        $this->getHtmlEditor()->setFieldLabel($title);
        return $this;
    }
    public function setHtmlEditorHeight($height)
    {
        $this->getHtmlEditor()->setHeight($height);
        return $this;
    }

    public function getHtmlEditor()
    {
        return $this->fields['content'];
    }

}
