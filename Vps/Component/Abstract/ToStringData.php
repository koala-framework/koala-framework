<?php
class Vps_Component_Abstract_ToStringData extends Vps_Data_Abstract implements Vps_Data_Vpc_ListInterface
{
    private $_subComponent = null;

    public function load($row)
    {
        //$row ist die von der parent, also zB der List
        $componentId = $row->component_id . '-' . $row->id;
        if ($this->_subComponent) {
            $componentId .= $this->_subComponent;
        }
        $c = Vps_Component_Data_Root::getInstance()
            ->getComponentByDbId($componentId, array('ignoreVisible'=>true));
        if (!$c) {
            $componentId = $row->component_id . '_' . $row->id;
            if ($this->_subComponent) {
                $componentId .= $this->_subComponent;
            }
            $c = Vps_Component_Data_Root::getInstance()
                ->getComponentByDbId($componentId, array('ignoreVisible'=>true));
        }
        if (!$c) return '';
        $admin = Vpc_Admin::getInstance($c->componentClass);
        return $admin->componentToString($c);
    }

    public function setSubComponent($key)
    {
        $this->_subComponent = $key;
    }
    public function getSubComponent()
    {
        return $this->_subComponent;
    }
}
