<?php
class Kwc_Directories_List_Trl_Cc_Component extends Kwc_Directories_List_Cc_Component
{
    public function getSelect()
    {
        $itemDirectory = $this->getItemDirectory();
        if (!$itemDirectory) return null;
        if (is_string($itemDirectory)) {
            throw new Kwf_Exception_NotYetImplemented();
        } else {
            if (Kwc_Abstract::getSetting($this->getData()->chained->chained->componentClass, 'useDirectorySelect')) {
                $ret = $itemDirectory->getComponent()->getSelect();
            } else {
                $ret = $itemDirectory->getGenerator('detail')
                    ->select($this->getItemDirectory());
            }
        }
        return $ret;
    }
}
