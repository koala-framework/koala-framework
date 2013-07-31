<?php
class Kwf_Component_Theme_Abstract extends Kwf_Component_Abstract
{
    public static function applySettings(&$ret, $theme)
    {
        $settings = call_user_func(array($theme, 'getRootSettings'));
        foreach ($settings as $k=>$i) {
            if (!isset($ret[$k]) || !is_array($ret[$k])) {
                $ret[$k] = $i;
            } else {
                $ret[$k] = array_merge($ret[$k], $i);
            }
        }
    }
}
