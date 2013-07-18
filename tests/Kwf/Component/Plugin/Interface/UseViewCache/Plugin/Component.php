<?php
class Kwf_Component_Plugin_Interface_UseViewCache_Plugin_Component extends Kwf_Component_Plugin_Abstract
    implements Kwf_Component_Plugin_Interface_UseViewCache
{
    public static $useViewCache = true;

    public function useViewCache($renderer)
    {
        return Kwf_Component_Plugin_Interface_UseViewCache_Plugin_Component::$useViewCache;
    }
}
