<?php
class Vpc_Basic_LinkTag_Abstract_Cc_Component extends Vpc_Chained_Cc_Component
{
    public function hasContent()
    {
        if ($this->getData()->url) {
            return true;
        } else {
            return false;
        }
    }
}
