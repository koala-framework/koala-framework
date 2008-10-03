<?php
class Vpc_Box_InheritContent_Content extends Vpc_Box_InheritContent_Component
{
    public function hasContent()
    {
        if ($this->getData()->componentId == 'root_page1-ic-child') {
            return false;
        } else if ($this->getData()->componentId == 'root_page1_page2-ic-child') {
            return true;
        } else if ($this->getData()->componentId == 'root_page1_page2_page3-ic-child') {
            return false;
        } else if ($this->getData()->componentId == 'root-ic-child') {
            return true;
        } else {
            throw new Vps_Exception("Don't know If I have content for ({$this->getData()->componentId})");
        }
    }
}
