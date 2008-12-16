<?php
class Vpc_Basic_LinkTag_Abstract_Admin extends Vpc_Admin
{
    public function onRowUpdate($row)
    {
        parent::onRowUpdate($row);
        if ($row->getModel() instanceof Vpc_Basic_LinkTag_Abstract_Model) {
            foreach (Vpc_Abstract::getComponentClasses() as $componentClass) {
                if (is_instance_of($componentClass, 'Vpc_Menu_Abstract')) {
                    $page = Vps_Component_Data_Root::getInstance()
                        ->getComponentById($row->component_id, array('ignoreVisible' => true));
                    if ($page && $page->parent->isPage) {
                        Vps_Component_Cache::getInstance()
                            ->cleanComponentClass($componentClass);
                    }
                }
            }
        }
    }
}
