<?php
class Kwc_Trl_InheritContent_Box_Child_Trl_Component extends Kwc_Chained_Trl_Component
{
    public function hasContent()
    {
        if ($this->getData()->componentId == 'root-en-box-child') {
            return true;
        }
        return false;
    }
}
