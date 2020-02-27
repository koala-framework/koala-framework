<?php
//this plugin replaces %redirect% dynamic (so viewCache can be enabled)
class Kwc_User_Login_Plugin extends Kwf_Component_Plugin_Abstract
    implements Kwf_Component_Plugin_Interface_ViewAfterChildRender
{
    public function processOutput($output, $renderer)
    {
        if (strpos($output, '%redirect%') !== false) {
            $r = isset($_GET['redirect']) ? $_GET['redirect'] : '';
            // this replace is only meant for value attribute in input-tag
            $output = str_replace('%redirect%', Kwf_Util_HtmlSpecialChars::filter($r), $output);
        }
        return $output;
    }
}
