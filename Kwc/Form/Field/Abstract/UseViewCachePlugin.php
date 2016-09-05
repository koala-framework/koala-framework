<?php
class Kwc_Form_Field_Abstract_UseViewCachePlugin extends Kwf_Component_Plugin_Abstract
    implements Kwf_Component_Plugin_Interface_UseViewCache
{
    public function useViewCache($renderer)
    {
        // Checking for specific post-data because there could be more forms on
        // one page and only one gets submited
        $id = $this->_componentId;
        while (strrpos($id, '-') !== false) {
            if (strrpos($id, '_') > strrpos($id, '-')) {
                //new page
                break;
            }
            $id = substr($id, 0, strrpos($id, '-'));
            if (isset($_REQUEST[$id.'-post'])) {
                return false;
            }
        }
        return true;
    }
}
