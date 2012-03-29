<?php
class Kwc_Advanced_GoogleMap_Update_37685 extends Kwf_Update
{
    public function postUpdate()
    {
        $components = Kwf_Component_Data_Root::getInstance()
            ->getComponentsByClass('Kwc_Advanced_GoogleMap_Component');
        foreach ($components as $c) {
            $r = $c->getComponent()->getRow();
            $r->map_type = 'map';
            $r->save();
        }
    }
}
