<?php
class Kwc_User_Login_Form_UseViewCachePlugin extends Kwf_Component_Plugin_Abstract
    implements Kwf_Component_Plugin_Interface_UseViewCache
{
    public function useViewCache($renderer)
    {
        return !isset($_REQUEST['redirect']);
    }
}
