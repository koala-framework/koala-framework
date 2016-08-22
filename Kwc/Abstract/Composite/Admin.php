<?php
class Kwc_Abstract_Composite_Admin extends Kwc_Admin
{
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
