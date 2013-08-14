<?php
class Kwc_Form_UseViewCachePlugin extends Kwf_Component_Plugin_Abstract
    implements Kwf_Component_Plugin_Interface_UseViewCache, Kwf_Component_Plugin_Interface_SkipProcessInput
{
    public function useViewCache($renderer)
    {
        // Checking for specific post-data because there could be more forms on
        // one page and only one gets submited
        if (isset($_REQUEST[$this->_componentId.'-post'])) {
            return false;
        } else {
            return true;
        }
    }

    public function skipProcessInput()
    {
        if (isset($_REQUEST[$this->_componentId.'-post'])) {
            return self::SKIP_NONE;
        } else {
            return self::SKIP_SELF;
        }
    }
}
