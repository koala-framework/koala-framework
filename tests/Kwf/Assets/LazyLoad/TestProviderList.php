<?php
class Kwf_Assets_LazyLoad_TestProviderList extends Kwf_Assets_ProviderList_Abstract
{
    public function __construct()
    {
        parent::__construct(array(
            new Kwf_Assets_Provider_JsClassKwf(),
            new Kwf_Assets_Provider_JsClass(KWF_PATH.'/tests/Kwf/Assets/LazyLoad', 'kwf/tests/Kwf/Assets/LazyLoad', 'Kwf.Assets.LazyLoad'),
            new Kwf_Assets_Provider_Ini(dirname(__FILE__).'/TestDependencies.ini'),
            new Kwf_Assets_Provider_IniNoFiles(),
        ));
    }
}
