<?php
class Kwc_Basic_LinkTag_Abstract_Data extends Kwf_Component_Data
{
    public function getLinkTitle()
    {
        $parent = $this->parent;
        if (is_instance_of($parent->componentClass, 'Kwc_Basic_LinkTag_Component')) {
            return $this->parent->getComponent()->getLinkTitle();
        }
        return parent::getLinkTitle();
    }
}
