<?php
class Kwf_View_Helper_HiddenOptions
{
    public function hiddenOptions($options, $class = 'options')
    {
        $options = Kwf_Util_HtmlSpecialChars::filter(json_encode($options));
        return '<input type="hidden" class="'.$class.'" value="'.$options.'" />';
    }
}
