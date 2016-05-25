<?php
class Kwf_Assets_Filter_Css_Ie8Remove extends Kwf_Assets_Filter_Css_AbstractPostCss
{
    public function getExecuteFor()
    {
        return self::EXECUTE_FOR_DEPENDENCY;
    }

    public function getPluginName()
    {
        return 'Kwf/Assets/Filter/Css/Ie8Remove';
    }

    public function getPluginOptions()
    {
        return array(
        );
    }

    public function getMasterFiles()
    {
        return array(
            getcwd().'/'.KWF_PATH.'/Kwf/Assets/Filter/Css/Ie8Remove.js'
        );
    }
}
