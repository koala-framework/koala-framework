<?php
class Kwc_Statistics_CookieAfterPlugin extends Kwf_Component_Plugin_Abstract
    implements Kwf_Component_Plugin_Interface_ViewBeforeChildRender
{
    public function processOutput($output, $renderer)
    {
        $pos = strpos($output, '{/kwcOptType}');
        if ($pos) {
            $optType = substr($output, 12, $pos - 12);
            if (Kwf_Statistics::isOptedIn($optType)) {
                return substr($output, $pos + 13);
            }
        }
        return '';
    }
}
