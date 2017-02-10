<?php
abstract class Kwc_Directories_List_Component extends Kwc_Abstract_Composite_Component
{
    private $_itemDirectory = false;

    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['child']['component']['view'] = 'Kwc_Directories_List_ViewPage_Component';
        $ret['useDirectorySelect'] = true;
        $ret['generatorJoins'] = false;
        return $ret;
    }

    final public function getItemDirectory()
    {
        if ($this->_itemDirectory === false) {
            $this->_itemDirectory = $this->_getItemDirectory();
            if (!$this->_itemDirectory) return $this->_itemDirectory;
            $isData = call_user_func(array(get_class($this), 'getItemDirectoryIsData'), $this->getData()->componentClass);
            if (is_string($this->_itemDirectory)) {
                if ($isData) {
                    throw new Kwf_Exception("_getItemDirectory returns string, so getItemDirectoryIsData must return false; componentClass is ".get_class($this));
                }
                $c = $this->_itemDirectory;
            } else {
                if (!$isData) {
                    throw new Kwf_Exception("_getItemDirectory returns data, so getItemDirectoryIsData must return true; componentClass is ".get_class($this));
                }
                $c = $this->_itemDirectory->componentClass;
            }
            if (!is_instance_of($c, 'Kwc_Directories_Item_DirectoryNoAdmin_Component')) {
                throw new Kwf_Exception("_getItemDirectory must return an Kwc_Directories_Item_DirectoryNoAdmin_Component data object or class-name, '{$c}' given; componentClass is ".get_class($this));
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
        return Kwc_Abstract::getSetting($this->_getItemDirectoryClass(), $setting);
    }

    public static function getItemDirectoryIsData($directoryClass)
    {
        return true;
    }

    abstract protected function _getItemDirectory();
    //abstract public static function getItemDirectoryClasses($componentClass);

    protected static function _getParentItemDirectoryClasses($componentClass, $steps = null)
    {
        $ret = array();
        foreach (Kwc_Abstract::getComponentClasses() as $class) {
            foreach (Kwc_Abstract::getChildComponentClasses($class) as $childClass) {
                if ($childClass == $componentClass) {
                    if ($steps === 0) {
                        $ret[] = $class;
                    } else if (is_null($steps)) {
                        if (is_instance_of($class, 'Kwc_Directories_Item_Directory_Component')) {
                            $ret[] = $class;
                        }
                    } else {
                        $ret = array_merge(
                            $ret,
                            self::_getParentItemDirectoryClasses($class, $steps - 1)
                        );
                    }
                }
            }
        }
        return $ret;
    }

    public function getSelect()
    {
        $itemDirectory = $this->getItemDirectory();
        if (!$itemDirectory) return null;
        if (is_string($itemDirectory)) {
            if ($this->_getSetting('useDirectorySelect')) {
                throw new Kwf_Exception("If itemDirectory is a ComponentClass you can't use 'useDirectorySelect' setting");
            }
            $c = Kwc_Abstract::getComponentClassByParentClass($itemDirectory);
            $ret = Kwf_Component_Generator_Abstract::getInstance($c, 'detail')
                ->select(null);
        } else {
            if ($this->_getSetting('useDirectorySelect')) {
                $ret = $itemDirectory->getComponent()->getSelect();
            } else {
                $ret = $itemDirectory->getGenerator('detail')
                    ->select($this->getItemDirectory());
            }
        }
        if (Kwc_Abstract::hasSetting($this->getData()->componentClass, 'order')) {
            throw new Kwf_Exception("Setting 'order' (".get_class($this).") doesn't exist anymore - overwrite getSelect for a custom order");
        }
        return $ret;
    }

    public final function callModifyItemData(Kwf_Component_Data $item)
    {
        foreach (Kwc_Abstract::getChildComponentClasses($this->getData()->componentClass) as $c) {
            if (Kwc_Abstract::hasSetting($c, 'hasModifyItemData')
                && Kwc_Abstract::getSetting($c, 'hasModifyItemData')) {
                call_user_func(array(strpos($c, '.') ? substr($c, 0, strpos($c, '.')) : $c, 'modifyItemData'), $item, $c);
            }
        }
    }

    public static function getViewCacheLifetimeForView()
    {
        return null;
    }

    public function getItemIds($select = null)
    {
        $itemDirectory = $this->getItemDirectory();
        if (is_string($itemDirectory)) {
            $c = Kwc_Abstract::getComponentClassByParentClass($itemDirectory);
            $generator = Kwf_Component_Generator_Abstract::getInstance($c, 'detail');
            $items = $generator->getChildIds(null, $select);
        } else {
            $items = $itemDirectory->getChildIds($select);
        }
        return $items;
    }

    public function getItems($select = null)
    {
        $itemDirectory = $this->getItemDirectory();
        if (is_string($itemDirectory)) {
            $c = Kwc_Abstract::getComponentClassByParentClass($itemDirectory);
            $generator = Kwf_Component_Generator_Abstract::getInstance($c, 'detail');
            $items = $generator->getChildData(null, $select);
        } else {
            $select->whereGenerator('detail');
            $items = $itemDirectory->getChildComponents($select);
        }
        foreach ($items as &$item) {
            $item->parent->getComponent()->callModifyItemData($item);
        }
        return $items;
    }
}
