<?php
class Kwc_Posts_Write_Trl_Component extends Kwc_Abstract_Composite_Trl_Component
{
    public function getPostsModel()
    {
        return $this->getData()->chained->parent->getComponent()->getChildModel();
    }

}
