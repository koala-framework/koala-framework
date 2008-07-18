<?php
class Vpc_News_List_Abstract_View_Admin extends Vpc_Admin
{
    public function clearCache($caller)
    {
        if ($caller instanceof Vpc_News_Directory_Row) {
            $components = Vps_Component_Data_Root::getInstance()->getComponentsByClass('Vpc_News_List_Abstract_View_Component');
            foreach ($components as $c) {
                Vps_Component_Cache::getInstance()->remove($c->componentId);
            }
        }
    }
}
