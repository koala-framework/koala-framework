<?php
class Kwf_Assets_Filter_Css_SelectorReplace extends Kwf_Assets_Filter_Css_AbstractPostCss
{
    protected $_replacements;

    public function __construct(array $replacements)
    {
        $this->_replacements = $replacements;
    }

    public function getExecuteFor()
    {
        return self::EXECUTE_FOR_DEPENDENCY;
    }

    public function getPluginName()
    {
        return 'Kwf/Assets/Filter/Css/SelectorReplace';
    }

    public function getPluginOptions(Kwf_Assets_Dependency_Abstract $dependency = null)
    {
        return array(
            'replacements' => $this->_replacements
        );
    }

    public function getMasterFiles()
    {
        return array(
            getcwd().'/'.KWF_PATH.'/Kwf/Assets/Filter/Css/SelectorReplace.js'
        );
    }
}
