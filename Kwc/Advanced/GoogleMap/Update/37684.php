<?php
class Kwc_Advanced_GoogleMap_Update_37684 extends Kwf_Update
{
    public function update()
    {
        $components = Kwf_Component_Data_Root::getInstance()
            ->getComponentsByClass('Kwc_Advanced_GoogleMap_Component');
        foreach ($components as $c) {
            $r = $c->getComponent()->getRow();
            $r->routing = 1;
            $r->save();
        }
    }
}
