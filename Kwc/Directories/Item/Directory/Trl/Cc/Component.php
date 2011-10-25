<?php
class Kwc_Directories_Item_Directory_Trl_Cc_Component extends Kwc_Directories_Item_Directory_Cc_Component
{
    protected function _getChainedComponent()
    {
        return $this->getData()->chained->chained;
    }
}
