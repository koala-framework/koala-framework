<?php
class Vpc_Advanced_SearchEngineReferer_Admin extends Vpc_Abstract_Composite_Admin
{
    public function onRowInsert($row)
    {
        $modelname = Vpc_Abstract::getSetting($this->_class, 'modelname');
        if ($row instanceof Vps_Model_Row_Interface
            && (is_instance_of($row->getModel(), $modelname))
        ) {
            $components = Vps_Component_Data_Root::getInstance()
                ->getComponentsByDbId($row->component_id, array('ignoreVisible'=>true));
            foreach ($components as $c) {
                $cc = $c->getChildComponents(array('componentClass'=>$this->_class));
                Vps_Component_Cache::getInstance()->remove($cc);
            }
        }
    }
}
