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
                    $dirModel = Kwc_Abstract::createChildModel($dirCls);
                    $relModel = $dirModel->getDependentModel($childReference);
                    $ret[] = array(
                        'class' => $relModel,
                        'event' => 'Kwf_Events_Event_Row_Updated',
                        'callback' => 'onUpdateRow'
                    );
                    $ret[] = array(
                        'class' => $relModel,
                        'event' => 'Kwf_Events_Event_Row_Inserted',
                        'callback' => 'onUpdateRow'
                    );
                    $ret[] = array(
                        'class' => $relModel,
                        'event' => 'Kwf_Events_Event_Row_Deleted',
                        'callback' => 'onUpdateRow'
                    );
                }
            }
        }
        return $ret;
    }

    public function onUpdateRow(Kwf_Events_Event_Row_Abstract $ev)
    {
        foreach (call_user_func(array($this->_class, 'getItemDirectoryClasses'), $this->_class) as $dirCls) {
            $item = $ev->row->getModel()->getReference('Item');
            $itemId = $ev->row->{$item['column']};
            $this->fireEvent(new Kwc_Directories_List_EventItemUpdated($dirCls, $itemId));
        }
    }
}
