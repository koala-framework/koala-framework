<?php
class Kwc_Directories_Item_Directory_Trl_Cc_Component extends Kwc_Directories_Item_Directory_Cc_Component
{
    public function getItemDirectory()
    {
        return self::getChainedByMaster(
            $this->getData()->chained->getComponent()->getItemDirectory(),
            $this->getData(),
            array('ignoreVisible' => true)
        );
    }

    protected function _getChainedComponent()
    {
        return $this->getData()->chained->chained;
    }
}
