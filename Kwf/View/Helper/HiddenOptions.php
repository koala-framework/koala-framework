<?php
class Vps_View_Helper_HiddenOptions
{
    public function hiddenOptions($options, $class = 'options')
    {
        $options = htmlspecialchars(json_encode($options));
        return '<input type="hidden" class="'.$class.'" value="'.$options.'" />';
    }
}
