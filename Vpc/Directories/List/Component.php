<?php
abstract class Vpc_Directories_List_Component extends Vpc_Abstract_Composite_Component
{
    private $_itemDirectory = false;

    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['view'] = 'Vpc_Directories_List_ViewPage_Component';
        $ret['useDirectorySelect'] = true;
        $ret['generatorJoins'] = false;
        return $ret;
    }

    final public function getItemDirectory()
    {
        if ($this->_itemDirectory === false) {
            $this->_itemDirectory = $this->_getItemDirectory();
            if (!$this->_itemDirectory) return $this->_itemDirectory;
            if (is_string($this->_itemDirectory)) {
                $c = $this->_itemDirectory;
            } else {
                $c = $this->_itemDirectory->componentClass;
            }
            if (!is_instance_of($c, 'Vpc_Directories_Item_Directory_Component')) {
                throw new Vps_Exception("_getItemDirectory must return an Vpc_Directories_Item_Directory_Component data object or class-name, '{$c}' given; componentClass is ".get_class($this));
            }
        }
        return $this->_itemDirectory;
    }

    final protected function _getItemDirectoryClass()
    {
        $ret = $this->getItemDirectory();
        if (is_object($ret)) $ret = $ret->componentClass;
        return $ret;
    }
    final protected function _getItemDirectorySetting($setting)
    {
        return Vpc_Abstract::getSetting($this->_getItemDirectoryClass(), $setting);
    }

    abstract protected function _getItemDirectory();

    public function getSelect($overrideValues = array())
    {
        $itemDirectory = $this->getItemDirectory();
        if (!$itemDirectory) return null;
        if (is_string($itemDirectory)) {
            if ($this->_getSetting('useDirectorySelect')) {
                throw new Vps_Exception("If itemDirectory is a ComponentClass you can't use 'useDirectorySelect' setting");
            }
            $c = Vpc_Abstract::getComponentClassByParentClass($itemDirectory);
            $ret = Vps_Component_Generator_Abstract::getInstance($c, 'detail')
                ->select(null);
        } else {
            if ($this->_getSetting('useDirectorySelect')) {
                $ret = $itemDirectory->getComponent()->getSelect($overrideValues);
            } else {
                $ret = $itemDirectory->getGenerator('detail')
                    ->select($this->getItemDirectory());
            }
        }
        if (Vpc_Abstract::hasSetting($this->getData()->componentClass, 'order')) {
            throw new Vps_Exception("Setting 'order' (".get_class($this).") doesn't exist anymore - overwrite getSelect for a custom order");
        }
        return $ret;
    }

    public final function callModifyItemData(Vps_Component_Data $item)
    {
        foreach (Vpc_Abstract::getChildComponentClasses($this->getData()->componentClass) as $c) {
            if (Vpc_Abstract::hasSetting($c, 'hasModifyItemData')
                && Vpc_Abstract::getSetting($c, 'hasModifyItemData')) {
                call_user_func(array(strpos($c, '.') ? substr($c, 0, strpos($c, '.')) : $c, 'modifyItemData'), $item, $c);
            }
        }
    }

    public static function getViewCacheLifetimeForView()
    {
        return null;
    }
}
