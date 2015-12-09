<?php
class Kwf_Assets_Filter_Css_MediaQueriesDropRedundant extends Kwf_Assets_Filter_Css_AbstractPostCss
{
    public function getExecuteFor()
    {
        return self::EXECUTE_FOR_DEPENDENCY;
    }

    public function getPluginName()
    {
        return 'autoprefixer';
    }
}

