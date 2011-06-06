<?php
class Vpc_Advanced_GoogleMap_Update_37684 extends Vps_Update
{
    public function update()
    {
        $components = Vps_Component_Data_Root::getInstance()
            ->getComponentsByClass('Vpc_Advanced_GoogleMap_Component');
        foreach ($components as $c) {
            $r = $c->getComponent()->getRow();
            $r->routing = 1;
            $r->save();
        }
    }
}
