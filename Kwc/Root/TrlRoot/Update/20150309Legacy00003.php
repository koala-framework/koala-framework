<?php
class Kwc_Root_TrlRoot_Update_20150309Legacy00003 extends Kwf_Update
{
    public function postUpdate()
    {
        foreach (Kwc_Abstract::getComponentClasses() as $c) {
            if (is_instance_of($c, 'Kwc_Root_TrlRoot_Component')) {
                if ($m = Kwc_Abstract::createChildModel($c)) {
                    foreach ($m->getRows() as $r) {
                        $r->visible = true;
                        $r->save();
                    }
                }
            }
        }
    }
}
