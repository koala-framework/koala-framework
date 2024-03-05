<?php
class Kwc_Basic_Text_Form extends Kwc_Abstract_Form
{
    public function __construct($name, $class, $id = null)
    {
        $this->setModel(Kwc_Basic_Text_Component::createOwnModel($class));
        parent::__construct($name, $class, $id);
        $field = new Kwf_Form_Field_HtmlEditor('content', trlKwf('Text'));
        $field->setData(new Kwc_Basic_Text_ContentData('content'));
        $field->setHideLabel(true);

        $ignoreSettings = array('tablename', 'componentName',
                'default', 'assets', 'assetsAdmin',
                'placeholder', 'plugins');
        $c = strpos($class, '.') ? substr($class, 0, strpos($class, '.')) : $class;
        foreach (call_user_func(array($c, 'getSettings')) as $key => $val) {
            if (!in_array($key, $ignoreSettings)) {
                $method = 'set' . ucfirst($key);
                $field->$method($val);
            }
        }
        $generators = Kwc_Abstract::getSetting($this->getClass(), 'generators');
        $classes = isset($generators['child']) ? $generators['child']['component'] : array();
        if (isset($classes['link']) && $classes['link']) {
            $cfg = new Kwf_Component_Abstract_ExtConfig_Form($classes['link']);
            $c = $cfg->getConfig(Kwf_Component_Abstract_ExtConfig_Abstract::TYPE_DEFAULT);
            $field->setLinkComponentConfig($c['form']);
        }
        if (isset($classes['image']) && $classes['image']) {
            $c = Kwc_Admin::getInstance($classes['image'])->getExtConfig();
            $field->setImageComponentConfig($c['form']);
        }
        if (isset($classes['download']) && $classes['download']) {
            $c = Kwc_Admin::getInstance($classes['download'])->getExtConfig();
            $field->setDownloadComponentConfig($c['form']);
        }
        if (Kwc_Abstract::getSetting($this->getClass(), 'enableStylesEditor')) {
            $admin = Kwc_Admin::getInstance($class);
            $field->setStylesEditorConfig(array(
                'xtype' => 'kwc.basic.text.styleseditor',
                'blockStyleUrl' => $admin->getControllerUrl('BlockStyle'),
                'inlineStyleUrl' => $admin->getControllerUrl('InlineStyle'),
                'masterStyleUrl' => $admin->getControllerUrl('MasterStyle')
            ));
        }

        $t = Kwf_Model_Abstract::getInstance(Kwc_Abstract::getSetting($class, 'stylesModel'));
        $field->setStyles($t->getStyles());
        $field->setComponentClass($class);

        $field->setControllerUrl(Kwc_Admin::getInstance($class)->getControllerUrl());
        $this->fields->add($field);

        $t = Kwf_Model_Abstract::getInstance(Kwc_Abstract::getSetting($this->getClass(), 'stylesModel'));

        $urls = array();
        if (Kwf_Assets_WebpackConfig::getDevServerUrl()) {
            $urls[] = Kwf_Assets_WebpackConfig::getDevServerUrl() . 'assets/build/Frontend.css';
        } else {
            $urls[] = '/assets/build/Frontend.css';
        }
        $styleEditorUrl = Kwc_Admin::getInstance($this->getClass())->getControllerUrl().'/styles-content';
        $styleEditorUrl .= '?t='.$t->getMTime();
        $urls[] = $styleEditorUrl;

        $this->fields['content']->setStylesCssFile($styleEditorUrl);
        $this->fields['content']->setCssFiles($urls);
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
    public function setHtmlEditorWidth($width)
    {
        $this->getHtmlEditor()->setWidth($width);
        return $this;
    }

    public function getHtmlEditor()
    {
        return $this->fields['content'];
    }

}
