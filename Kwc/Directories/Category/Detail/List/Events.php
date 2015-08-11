<?php
class Kwc_Directories_Category_Detail_List_Events extends Kwc_Abstract_Composite_Events
{
    private function _canCreateUsIndirectly($class)
    {
        static $cache = array();
        if (isset($cache[$class])) return $cache[$class];
        foreach (Kwc_Abstract::getChildComponentClasses($class) as $c) {
            if ($c == $this->_class) {
                $cache[$class] = true;
                return true;
            }
            if ($this->_canCreateUsIndirectly($c)) {
                return true;
            }
        }
        $cache[$class] = false;
        return false;
    }

    public function getListeners()
    {
        $ret = parent::getListeners();
        foreach (Kwc_Abstract::getComponentClasses() as $class) {
            if (is_instance_of($class, 'Kwc_Directories_Category_Directory_Component') && $this->_canCreateUsIndirectly($class)) {
                $childReference =
                    Kwc_Abstract::hasSetting($class, 'childReferenceName') ?
                    Kwc_Abstract::getSetting($class, 'childReferenceName') :
                    'Categories';
                foreach (call_user_func(array($this->_class, 'getItemDirectoryClasses'), $this->_class) as $dirCls) {
                    $dirModel = Kwc_Abstract::getSetting($dirCls, 'childModel');
                    $relModel = Kwf_Model_Abstract::getInstance($dirModel)->getDependentModel($childReference);
                    $ret[] = array(
                        'class' => $relModel,
                        'event' => 'Kwf_Component_Event_Row_Updated',
                        'callback' => 'onUpdateRow'
                    );
                    $ret[] = array(
                        'class' => $relModel,
                        'event' => 'Kwf_Component_Event_Row_Inserted',
                        'callback' => 'onUpdateRow'
                    );
                    $ret[] = array(
                        'class' => $relModel,
                        'event' => 'Kwf_Component_Event_Row_Deleted',
                        'callback' => 'onUpdateRow'
                    );
                }
            }
        }
        return $ret;
    }

    private function _getSubrootFromItemId($dirCls, $itemId)
    {
        $gen = Kwf_Component_Generator_Abstract::getInstance($dirCls, 'detail');
        $datas = $gen->getChildData(null, array('id' => $itemId, 'ignoreVisible' => true));
        if (!isset($datas[0])) return null;
        return $datas[0]->getSubroot();
    }

    public function onUpdateRow(Kwf_Component_Event_Row_Abstract $ev)
    {
        foreach (call_user_func(array($this->_class, 'getItemDirectoryClasses'), $this->_class) as $dirCls) {
            $item = $ev->row->getModel()->getReference('Item');
            $itemId = $ev->row->{$item['column']};
            $subroot = $this->_getSubrootFromItemId($dirCls, $itemId);
            if ($subroot) {
                $this->fireEvent(new Kwc_Directories_List_EventItemUpdated($dirCls, $itemId, $subroot));
            }
        }
    }
}
