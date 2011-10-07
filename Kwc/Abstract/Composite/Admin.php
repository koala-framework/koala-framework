<?php
class Vpc_Abstract_Composite_Admin extends Vpc_Admin
{
    public function delete($componentId)
    {
        parent::delete($componentId);
        $classes = Vpc_Abstract::getChildComponentClasses($this->_class, 'child');
        if (Vpc_Abstract::hasSetting($this->_class, 'tablename')) {
            //wenn komponente kein model hat unterkomponenten hier löschen
            //ansonsten erledigt das die row
            foreach ($classes as $k=>$i) {
                Vpc_Admin::getInstance($i)->delete($componentId.'-'.$k);
            }
        }
        $where = array();
    }

    public function gridColumns()
    {
        $ret = parent::gridColumns();
        unset($ret['string']);
        $classes = Vpc_Abstract::getChildComponentClasses($this->_class, 'child');
        foreach ($classes as $key => $class) {
            $columns = Vpc_Admin::getInstance($class)->gridColumns();
            foreach ($columns as $k => $column) {
                $column->setDataIndex($key.'_'.$column->getDataIndex());
                $childData = $column->getData();
                if ($childData instanceof Vps_Data_Vpc_ListInterface) {
                    $childData->setSubComponent('-'.$key);
                    $ret[$key.'_'.$k] = $column->setData(
                        new Vpc_Abstract_Composite_ChildData($childData)
                    );
                }
            }
        }
        return $ret;
    }
}
