<?php
class Vpc_Basic_Text_Form extends Vpc_Abstract_Form
{
    public function __construct($name, $class, $id = null)
    {
        $this->setModel(Vpc_Basic_Text_Component::getTextModel($class));
        parent::__construct($name, $class, $id);
        $field = new Vps_Form_Field_HtmlEditor('content', trlVps('Text'));
        $field->setData(new Vps_Data_Vpc_ComponentIds('content'));
        $field->setHideLabel(true);

        $ignoreSettings = array('tablename', 'componentName',
                'default', 'assets', 'assetsAdmin',
                'placeholder');
        foreach (call_user_func(array($class, 'getSettings')) as $key => $val) {
            if (!in_array($key, $ignoreSettings)) {
                $method = 'set' . ucfirst($key);
                $field->$method($val);
            }
        }
        $generators = Vpc_Abstract::getSetting($this->getClass(), 'generators');
        $classes = $generators['child']['component'];
        if ($classes['link']) {
            $c = Vpc_Admin::getInstance($classes['link'])->getExtConfig();
            $field->setLinkComponentConfig($c['form']);
        }
        if ($classes['image']) {
            $c = Vpc_Admin::getInstance($classes['image'])->getExtConfig();
            $field->setImageComponentConfig($c['form']);
        }
        if ($classes['download']) {
            $c = Vpc_Admin::getInstance($classes['download'])->getExtConfig();
            $field->setDownloadComponentConfig($c['form']);
        }
        if (Vpc_Abstract::getSetting($this->getClass(), 'enableStylesEditor')) {
            $admin = Vpc_Admin::getInstance($class);
            $field->setStylesEditorConfig(array(
                'xtype' => 'vpc.basic.text.styleseditor',
                'blockStyleUrl' => $admin->getControllerUrl('BlockStyle'),
                'inlineStyleUrl' => $admin->getControllerUrl('InlineStyle'),
                'masterStyleUrl' => $admin->getControllerUrl('MasterStyle')
            ));
        }

        $t = Vps_Model_Abstract::getInstance(Vpc_Abstract::getSetting($class, 'stylesModel'));
        $field->setStyles($t->getStyles());
        $field->setComponentClass($class);

        $field->setControllerUrl(Vpc_Admin::getInstance($class)->getControllerUrl());
        $this->fields->add($field);

        $this->setAssetsType('Frontend');
    }

    //für tests
    public function setAssetsType($type)
    {
        $loader = new Vps_Assets_Loader();
        $dep = $loader->getDependencies();
        $urls = $dep->getAssetUrls($type, 'css', 'web', Vps_Component_Data_Root::getComponentClass());

        $this->fields['content']->setCssFiles($urls);

        foreach ($urls as $url) {
            if (strpos($url, 'Vpc_Basic_Text_StylesAsset')!==false) {
                $this->fields['content']->setStylesCssFile($url);
                break;
            }
        }
    }

    public function setHtmlEditorLabel($title)
    {
        $this->getHtmlEditor()
            ->setFieldLabel($title)
            ->setHideLabel(false);
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
