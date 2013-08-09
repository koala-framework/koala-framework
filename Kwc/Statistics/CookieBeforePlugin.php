<?php
/**
 * Plugin to remove component which uses cookie when user has chosen to opt-out from cookies
 *
 * Needs to used in conjunction with Kwc_Statistics_CookieAfterPlugin
 *
 * @see Kwc_Statistics_Analytics_Component
 */
class Kwc_Statistics_CookieBeforePlugin extends Kwf_Component_Plugin_Abstract
    implements Kwf_Component_Plugin_Interface_ViewBeforeCache
{
    public function processOutput($output, $renderer)
    {
        $component = Kwf_Component_Data_Root::getInstance()->getComponentById($this->_componentId);
        $output = '{kwcOptType}' . Kwf_Statistics::getDefaultOptValue($component) . '{/kwcOptType}' . $output;
        return $output;
    }
}
