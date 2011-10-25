<?php
class Kwc_Trl_InheritContentWithVisible_Box_Child_Trl_Component extends Kwc_Chained_Trl_Component
{
    public function hasContent()
    {
        if ($this->getData()->componentId == 'root-en-box-child'
            || $this->getData()->componentId == 'root-en_test_test2-box-child'
            || $this->getData()->componentId == 'root-en_test_test2_test3-box-child') {
            return true;
        }
        return false;
    }
}
