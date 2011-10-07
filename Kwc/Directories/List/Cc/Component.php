<?php
class Kwc_Directories_List_Cc_Component extends Kwc_Abstract_Composite_Cc_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);

        //child generator 1:1 übernehmen um die gleiche view zu haben (keine uebersetzte)
        $masterGen = Kwc_Abstract::getSetting($masterComponentClass, 'generators');
        $ret['generators']['child'] = $masterGen['child'];
        return $ret;
    }

    protected function _getChainedComponent()
    {
        return $this->getData()->chained;
    }

    public function getItemDirectory()
    {
        return self::getChainedByMaster(
            $this->_getChainedComponent()->getComponent()->getItemDirectory(),
            $this->getData(),
            array('ignoreVisible' => true)
        );
    }

    public function getSelect()
    {
        $itemDirectory = $this->getItemDirectory();
        if (!$itemDirectory) return null;
        if (is_string($itemDirectory)) {
            throw new Kwf_Exception_NotYetImplemented();
        } else {
            if (Kwc_Abstract::getSetting($this->_getChainedComponent()->componentClass, 'useDirectorySelect')) {
                $ret = $itemDirectory->getComponent()->getSelect();
            } else {
                $ret = $itemDirectory->getGenerator('detail')
                    ->select($this->getItemDirectory());
            }
        }
        return $ret;
    }

    public final function callModifyItemData(Kwf_Component_Data $item)
    {
        foreach (Kwc_Abstract::getChildComponentClasses($this->getData()->componentClass) as $c) {
            if (Kwc_Abstract::hasSetting($c, 'hasModifyItemData')
                && Kwc_Abstract::getSetting($c, 'hasModifyItemData')
            ) {
                call_user_func(array(strpos($c, '.') ? substr($c, 0, strpos($c, '.')) : $c, 'modifyItemData'), $item, $c);
            }
        }
    }

    public static function getViewCacheLifetimeForView()
    {
        //TODO?!
        return null;
    }
}


