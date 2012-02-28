<?php
class Vpc_Advanced_GoogleMap_Update_37685 extends Vps_Update
{
    public function postUpdate()
    {
        $components = Vps_Component_Data_Root::getInstance()
            ->getComponentsByClass('Vpc_Advanced_GoogleMap_Component');
        foreach ($components as $c) {
            $r = $c->getComponent()->getRow();
            $r->map_type = 'map';
            $r->save();
        }
    }
}
