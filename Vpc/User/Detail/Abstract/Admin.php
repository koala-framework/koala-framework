<?php
class Vpc_User_Detail_Abstract_Admin extends Vpc_Abstract_Composite_Admin
{
    public function onRowUpdate($row)
    {
        parent::onRowUpdate($row);
        if ($row instanceof Vps_Model_User_User) {
            $userDirs =  Vps_Component_Data_Root::getInstance()
                ->getComponentsByClass('Vpc_User_Directory_Component');
            foreach ($userDirs as $dir) {
                $detail = $dir->getChildComponent('_'.$row->id);
                foreach ($detail->getRecursiveChildComponents(array('componentClass'=>$this->_class)) as $c) {
                    Vps_Component_Cache::getInstance()->remove($this->_class, $c->componentId);
                }
            }
        }
    }
}
