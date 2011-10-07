<?php
class Vpc_Trl_InheritContent_Box_Child_Trl_Component extends Vpc_Chained_Trl_Component
{
    public function hasContent()
    {
        if ($this->getData()->componentId == 'root-en-box-child') {
            return true;
        }
        return false;
    }
}
