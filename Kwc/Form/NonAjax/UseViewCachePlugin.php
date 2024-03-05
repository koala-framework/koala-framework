<?php
class Kwc_Form_NonAjax_UseViewCachePlugin extends Kwf_Component_Plugin_Abstract
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

    public function skipProcessInput(Kwf_Component_Data $data)
    {
        if (isset($_REQUEST[$this->_componentId.'-post'])) {
            return false;
        } else {
            if ($this->_componentId == $data->componentId) {
                //skip form itself
                return true;
            } else {
                //don't skip other components
                return false;
            }
        }
    }
}
