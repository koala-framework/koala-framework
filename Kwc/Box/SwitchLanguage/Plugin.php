<?php
class Kwc_Box_SwitchLanguage_Plugin extends Kwf_Component_Plugin_Abstract
    implements Kwf_Component_Plugin_Interface_ViewAfterChildRender
{
    public function processOutput($output, $renderer)
    {
        if (isset($_SERVER['QUERY_STRING'])) {
            $output = preg_replace('/href="(.*)"/U', 'href="$1?' . $_SERVER['QUERY_STRING'] . '"', $output);
        }
        return $output;
    }
}
