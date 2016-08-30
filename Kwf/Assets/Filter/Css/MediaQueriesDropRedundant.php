<?php
class Kwf_Assets_Filter_Css_MediaQueriesDropRedundant extends Kwf_Assets_Filter_Css_AbstractPostCss
{
    public function getExecuteFor()
    {
        return self::EXECUTE_FOR_DEPENDENCY;
    }

    public function getPluginName()
    {
        return 'postcss-media-queries-drop-redundant';
    }

    public function getMasterFiles()
    {
        return array(
            getcwd().'/node_modules/postcss-media-queries-drop-redundant/package.json'
        );
    }
}

