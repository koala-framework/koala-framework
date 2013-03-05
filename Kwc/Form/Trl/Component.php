<?php
class Kwc_Form_Trl_Component extends Kwc_Chained_Trl_MasterAsChild_Component
{
    //we don't have processInput flag but forward it if
    public function processInput($postData)
    {
        $this->getData()->getChildComponent('-child')
            ->getComponent()->processInput($postData);
    }
}
