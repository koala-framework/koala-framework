<?php
class Vps_Component_DependingOnRows_TestComponent_Admin extends Vpc_Admin
    implements Vps_Component_Abstract_Admin_Interface_DependsOnRow
{
    public function getComponentsDependingOnRow(Vps_Model_Row_Interface $row)
    {
        //siehe PagesModel
        $map = array(
            2 => 10,
            20 => 20,
            30 => 31
        );

        if ($row->getModel() instanceof Vps_Component_DependingOnRows_PagesModel) {
            if (isset($map[$row->id])) {
                return array(Vps_Component_Data_Root::getInstance()
                    ->getComponentById($map[$row->id]));
            }
        }
        return array();
    }
}
