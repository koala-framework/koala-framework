<?php
class Kwf_Assets_Filter_Css_Autoprefixer extends Kwf_Assets_Filter_Css_AbstractPostCss
{
    public function getExecuteFor()
    {
        return self::EXECUTE_FOR_DEPENDENCY;
    }

    public function getPluginName()
    {
        return 'autoprefixer';
    }

    public function getPluginOptions()
    {
        return array(
            'browsers' => '> 0.05%',
            'remove' => true,
            'add' => true
        );
    }

    public function getMasterFiles()
    {
        return array(
            getcwd().'/'.KWF_PATH.'/node_modules/autoprefixer/package.json'
        );
    }
}
