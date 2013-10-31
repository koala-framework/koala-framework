<?php
class Kwf_Assets_Dependency_EmptyProviderList extends Kwf_Assets_ProviderList_Abstract
{
    public function __construct()
    {
        parent::__construct(array());
    }
}
