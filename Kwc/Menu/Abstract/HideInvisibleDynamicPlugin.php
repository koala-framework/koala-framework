<?php
class Kwc_Menu_Abstract_HideInvisibleDynamicPlugin extends Kwf_Component_Plugin_Abstract
    implements Kwf_Component_Plugin_Interface_ViewBeforeChildRender
{
    public function processOutput($output, $renderer)
    {
        if (!$output) return $output;

        if (strpos($output, '<!-- start ')=== false) return $output;

        return preg_replace_callback(
            '#<!-- start ([^ ]+) ([^ ]+) -->.*?<!-- end \1 \2 -->#s',
            array($this, '_removeInvisilbe'),
            $output
        );
    }

    public function _removeInvisilbe($m)
    {
        if (!call_user_func(array($m[2], 'isVisibleDynamic'), $m[1], $m[2])) {
            return '';
        } else {
            return $m[0];
        }
    }
}
