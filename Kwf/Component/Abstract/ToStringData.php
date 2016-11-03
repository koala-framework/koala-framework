<?php
class Kwf_Component_Abstract_ToStringData extends Kwf_Data_Abstract implements Kwf_Data_Kwc_ListInterface
{
    private $_subComponent = null;

    public function load($row, array $info = array())
    {
        //$row ist die von der parent, also zB der List
        $componentId = $row->component_id . '-' . $row->id;
        if ($this->_subComponent) {
            $componentId .= $this->_subComponent;
        }
        $c = Kwf_Component_Data_Root::getInstance()
            ->getComponentByDbId($componentId, array('ignoreVisible'=>true));
        if (!$c) {
            $componentId = $row->component_id . '_' . $row->id;
            if ($this->_subComponent) {
                $componentId .= $this->_subComponent;
            }
            $c = Kwf_Component_Data_Root::getInstance()
                ->getComponentByDbId($componentId, array('ignoreVisible'=>true));
        }
        if (!$c) return '';
        $admin = Kwc_Admin::getInstance($c->componentClass);
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
