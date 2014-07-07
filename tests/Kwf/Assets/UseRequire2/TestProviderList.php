<?php
class Kwf_Assets_UseRequire2_TestProviderList extends Kwf_Assets_ProviderList_Abstract
{
    public function __construct()
    {
        parent::__construct(array(
            new Kwf_Assets_UseRequire2_TestProvider(),
        ));
    }
}
