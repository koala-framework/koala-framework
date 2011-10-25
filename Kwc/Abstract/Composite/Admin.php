<?php
class Kwc_Abstract_Composite_Admin extends Kwc_Admin
{
    public function delete($componentId)
    {
        parent::delete($componentId);
        $classes = Kwc_Abstract::getChildComponentClasses($this->_class, 'child');
        if (Kwc_Abstract::hasSetting($this->_class, 'tablename')) {
            //wenn komponente kein model hat unterkomponenten hier löschen
            //ansonsten erledigt das die row
            foreach ($classes as $k=>$i) {
                Kwc_Admin::getInstance($i)->delete($componentId.'-'.$k);
            }
        }
        $where = array();
    }

    public function gridColumns()
    {
        $ret = parent::gridColumns();
        unset($ret['string']);
        $classes = Kwc_Abstract::getChildComponentClasses($this->_class, 'child');
        foreach ($classes as $key => $class) {
            $columns = Kwc_Admin::getInstance($class)->gridColumns();
            foreach ($columns as $k => $column) {
                $column->setDataIndex($key.'_'.$column->getDataIndex());
                $childData = $column->getData();
                if ($childData instanceof Kwf_Data_Kwc_ListInterface) {
                    $childData->setSubComponent('-'.$key);
                    $ret[$key.'_'.$k] = $column->setData(
                        new Kwc_Abstract_Composite_ChildData($childData)
                    );
                }
            }
        }
        return $ret;
    }
}
