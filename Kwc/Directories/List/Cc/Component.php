<?php
class Kwc_Directories_List_Cc_Component extends Kwc_Abstract_Composite_Cc_Component
{
    public static function getSettings($masterComponentClass = null)
    {
        $ret = parent::getSettings($masterComponentClass);

        //child generator 1:1 übernehmen um die gleiche view zu haben (keine uebersetzte)
        $masterGen = Kwc_Abstract::getSetting($masterComponentClass, 'generators');
        $ret['generators']['child'] = $masterGen['child'];
        return $ret;
    }

    public static function getItemDirectoryClasses($componentClass)
    {
        $masterCC = Kwc_Abstract::getSetting($componentClass, 'masterComponentClass');
        $ret = array();
        $c = strpos($masterCC, '.') ? substr($masterCC, 0, strpos($masterCC, '.')) : $masterCC;
        foreach (call_user_func(array($c, 'getItemDirectoryClasses'), $masterCC) as $masterDirCls) {
            $ret[] = self::getChainedComponentClass($masterDirCls, 'Cc');
        }
        return $ret;
    }

    public static function getItemDirectoryIsData($componentClass)
    {
        $masterCC = Kwc_Abstract::getSetting($componentClass, 'masterComponentClass');
        return call_user_func(array($masterCC, 'getItemDirectoryIsData'), $masterCC);
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
    public function getItems($select = null)
    {
        $ret = array();
        $items = $this->getData()->chained->getComponent()->getItems($select);
        foreach ($items as $item) {
            $trlItemCmp = self::getChainedByMaster($item, $this->getData(), array('ignoreVisible' => true));
            if ($trlItemCmp) {
                $trlItemCmp->parent->getComponent()->callModifyItemData($trlItemCmp);
                $ret[] = $trlItemCmp;
            }
        }
        return $ret;
    }

    public function getItemIds($select = null)
    {
        $ret = array();
        $items = $this->getItems($select);
        foreach ($items as $item) {
            if ($item) {
                $ret[] = $item->id;
            }
        }
        return $ret;
    }

    public function getSelect()
    {
        $itemDirectory = $this->getItemDirectory();
        if (!$itemDirectory) return null;
        if (is_string($itemDirectory)) {
            throw new Kwf_Exception_NotYetImplemented();
        } else {
            if (Kwc_Abstract::getSetting($this->_getChainedComponent()->componentClass, 'useDirectorySelect')) {
                $ret = $this->_getChainedComponent()->getComponent()->getSelect();
            } else {
                $ret = $this->_getChainedComponent()->getGenerator('detail')
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


