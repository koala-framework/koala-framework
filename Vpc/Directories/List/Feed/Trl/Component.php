<?php
class Vpc_Directories_List_Feed_Trl_Component extends Vpc_Chained_Trl_MasterAsChild_Component
{
    public function getItemDirectory()
    {
        return $this->getData()->parent->getComponent()->getItemDirectory();
    }

    public function getSelect()
    {
        return $this->getData()->parent->getComponent()->getSelect();
    }

    public function getCacheMeta()
    {
        throw new Vps_Exception_NotYetImplemented();
    }
}
