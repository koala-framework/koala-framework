<?php
class Kwc_Directories_List_Feed_Trl_Component extends Kwc_Chained_Trl_MasterAsChild_Component
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
        throw new Kwf_Exception_NotYetImplemented();
    }
}
