<?php
class Kwc_Form_UseViewCachePlugin extends Kwf_Component_Plugin_Abstract
    implements Kwf_Component_Plugin_Interface_UseViewCache
{
    public function useViewCache()
    {
        // Checking for specific post-data because there could be more forms on
        // one page and only one gets submited
        if (isset($_POST[$this->_componentId])) {
            return false;
        } else {
            return true;
        }
    }
}
