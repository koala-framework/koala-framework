<?php
class Kwf_Assets_Filter_Css_UniquePrefix extends Kwf_Assets_Filter_Css_SelectorReplace
{
    public function __construct($prefix = null)
    {
        $prefix = $prefix ? $prefix : Kwf_Config::getValue('application.uniquePrefix');
        parent::__construct(array(
            'kwfUp-' => $prefix
        ));
    }
}
