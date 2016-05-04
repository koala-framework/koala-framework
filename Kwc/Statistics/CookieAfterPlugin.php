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
        $pos = strpos($output, '{/kwcDefaultOptValue}');
        if ($pos) {
            $defaultOpt = substr($output, 20, $pos - 20);
            if ((!Kwf_CookieOpt::isSetOpt() && $defaultOpt == Kwf_CookieOpt::OPT_IN) ||
                Kwf_CookieOpt::getOpt() == Kwf_CookieOpt::OPT_IN
            ) {
                return substr($output, $pos + 21);
            }
        }
        return '';
    }
}
