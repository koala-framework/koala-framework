<?php
class Vpc_Directories_List_Cc_Component extends Vpc_Abstract_Composite_Cc_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);

        //child generator 1:1 übernehmen um die gleiche view zu haben (keine uebersetzte)
        $masterGen = Vpc_Abstract::getSetting($masterComponentClass, 'generators');
        $ret['generators']['child'] = $masterGen['child'];
        return $ret;
    }


    public function getItemDirectory()
    {
        return self::getChainedByMaster(
            $this->getData()->chained->getComponent()->getItemDirectory(),
            $this->getData(),
            array('ignoreVisible' => true)
        );
    }

    public function getSelect()
    {
        $itemDirectory = $this->getItemDirectory();
        if (!$itemDirectory) return null;
        if (is_string($itemDirectory)) {
            throw new Vps_Exception_NotYetImplemented();
        } else {
            if (Vpc_Abstract::getSetting($this->getData()->chained->componentClass, 'useDirectorySelect')) {
                $ret = $itemDirectory->getComponent()->getSelect();
            } else {
                $ret = $itemDirectory->getGenerator('detail')
                    ->select($this->getItemDirectory());
            }
        }
        return $ret;
    }

    public final function callModifyItemData(Vps_Component_Data $item)
    {
        foreach (Vpc_Abstract::getChildComponentClasses($this->getData()->chained->componentClass) as $c) {
            if (Vpc_Abstract::hasSetting($c, 'hasModifyItemData')
                && Vpc_Abstract::getSetting($c, 'hasModifyItemData')
            ) {
                call_user_func(array(strpos($c, '.') ? substr($c, 0, strpos($c, '.')) : $c, 'modifyItemData'), $item, $c);
            }
        }
    }

    public static function getViewCacheLifetimeForView()
    {
        return $this->getData()->chained->getComponent()->getViewCacheLifetimeForView();
    }
}


