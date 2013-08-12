<?php
/**
 * Plugin to remove component which uses cookie when user has chosen to opt-out from cookies
 *
 * Needs to used in conjunction with Kwc_Statistics_CookieBeforePlugin
 *
 * @see Kwc_Statistics_Analytics_Component
 */
class Kwc_Statistics_CookieAfterPlugin extends Kwf_Component_Plugin_Abstract
    implements Kwf_Component_Plugin_Interface_ViewBeforeChildRender
{
    public function processOutput($output, $renderer)
    {
        $pos = strpos($output, '{/kwcOptType}');
        if ($pos) {
            $optType = substr($output, 12, $pos - 12);
            if (
                (!Kwf_Statistics::issetUserOptValue() && $optType == Kwf_Statistics::OPT_OUT) ||
                Kwf_Statistics::getUserOptValue() == Kwf_Statistics::OPT_IN
            ) {
                return substr($output, $pos + 13);
            }
        }
        return '';
    }
}
