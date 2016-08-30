<?php
class Kwf_Assets_Filter_Css_PrefixerKeyframes extends Kwf_Assets_Filter_Css_AbstractPostCss
{
    protected $_prefix;

    public function __construct($prefix = null)
    {
        $this->_prefix = $prefix ? $prefix : Kwf_Config::getValue('application.uniquePrefix');
    }

    public function getExecuteFor()
    {
        return self::EXECUTE_FOR_DEPENDENCY;
    }

    public function getPluginName()
    {
        return 'postcss-prefixer-keyframes';
    }

    public function getPluginOptions()
    {
        return array(
            'prefix' => $this->_prefix
        );
    }

    public function getMasterFiles()
    {
        return array(
            getcwd().'/node_modules/postcss-prefixer-keyframes/package.json'
        );
    }
}
