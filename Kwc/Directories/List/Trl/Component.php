<?php
class Kwc_Directories_List_Trl_Component extends Kwc_Abstract_Composite_Trl_Component
{
    public static function getSettings($masterComponentClass = null)
    {
        $ret = parent::getSettings($masterComponentClass);

        //child generator 1:1 übernehmen um die gleiche view zu haben (keine uebersetzte)
        $masterGen = Kwc_Abstract::getSetting($masterComponentClass, 'generators');
        if (is_instance_of($masterGen['child']['component']['view'], 'Kwc_Directories_List_View_Component')) {
            $ret['generators']['child'] = $masterGen['child'];
        }
        return $ret;
    }

    public static function getItemDirectoryClasses($componentClass)
    {
        $masterCC = Kwc_Abstract::getSetting($componentClass, 'masterComponentClass');
        $ret = array();
        foreach (call_user_func(array($masterCC, 'getItemDirectoryClasses'), $masterCC) as $masterDirCls) {
            $ret[] = self::getChainedComponentClass($masterDirCls, 'Trl');
        }
        return $ret;
    }

    public static function getItemDirectoryIsData($componentClass)
    {
        $masterCC = Kwc_Abstract::getSetting($componentClass, 'masterComponentClass');
        return call_user_func(array($masterCC, 'getItemDirectoryIsData'), $masterCC);
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
            throw new Kwf_Exception_NotYetImplemented();
        } else {
            if (Kwc_Abstract::getSetting($this->getData()->chained->componentClass, 'useDirectorySelect')) {
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
        //TODO
        return null;
    }
}


