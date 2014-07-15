<?php
class Kwc_Advanced_Youtube_Update_1 extends Kwf_Update
{
    public function postUpdate()
    {
        parent::postUpdate();

        $components = Kwf_Component_Data_Root::getInstance()
            ->getComponentsByClass('Kwc_Advanced_Youtube_Component', array('ignoreVisible' => true));
    }
}

