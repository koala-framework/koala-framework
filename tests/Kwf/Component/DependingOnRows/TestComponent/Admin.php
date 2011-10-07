<?php
class Kwf_Component_DependingOnRows_TestComponent_Admin extends Kwc_Admin
    implements Kwf_Component_Abstract_Admin_Interface_DependsOnRow
{
    public function getComponentsDependingOnRow(Kwf_Model_Row_Interface $row)
    {
        //siehe PagesModel
        $map = array(
            2 => 10,
            20 => 20,
            30 => 31
        );

        if ($row->getModel() instanceof Kwf_Component_DependingOnRows_PagesModel) {
            if (isset($map[$row->id])) {
                return array(Kwf_Component_Data_Root::getInstance()
                    ->getComponentById($map[$row->id]));
            }
        }
        return array();
    }
}
