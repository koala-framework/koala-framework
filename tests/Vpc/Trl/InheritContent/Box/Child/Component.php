<?php
class Vpc_Trl_InheritContent_Box_Child_Component extends Vpc_Abstract
{
    public function hasContent()
    {
        if ($this->getData()->componentId == 'root-de-box-child' || $this->getData()->componentId == 'root-de_test_test2_test3-box-child') {
            return true;
        }
        return false;
    }
}
