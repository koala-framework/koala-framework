<?php
class Vpc_Root_TrlRoot_Update_3 extends Vps_Update
{
    public function postUpdate()
    {
        foreach (Vpc_Abstract::getComponentClasses() as $c) {
            if (is_instance_of($c, 'Vpc_Root_TrlRoot_Component')) {
                if ($m = Vpc_Abstract::createChildModel($c)) {
                    foreach ($m->getRows() as $r) {
                        $r->visible = true;
                        $r->save();
                    }
                }
            }
        }
    }
}
