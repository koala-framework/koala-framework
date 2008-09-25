<?php
class Vpc_User_Detail_Abstract_Admin extends Vpc_Abstract_Composite_Admin
{
    public function onRowUpdate($row)
    {
        parent::onRowUpdate($row);
        if ($row instanceof Vps_Model_User_User) {
            $userDetails =  Vps_Component_Data_Root::getInstance()
                ->getComponentsByClass('Vpc_User_Detail_Component', array('id'=>'_'.$row->id));
            foreach ($userDetails as $detail) {
                Vps_Component_Cache::getInstance()->remove(
                    $detail->getRecursiveChildComponents(array('componentClass'=>$this->_class))
                );
            }
        }
    }
}
