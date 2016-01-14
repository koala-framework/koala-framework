<?php
class Kwf_Assets_Filter_GeneralDepFilter_TestProviderList extends Kwf_Assets_ProviderList_Abstract
{
    public function __construct()
    {
        $providers = array(
            new Kwf_Assets_Filter_GeneralDepFilter_TestProvider(),
        );
        $filters = array(
            new Kwf_Assets_Filter_GeneralDepFilter_Filter()
        );
        parent::__construct($providers, $filters);
    }
}
