<?php
class Kwc_Posts_Write_Form_Trl_Component extends Kwc_Form_Trl_Component
{
    public function getPostsModel()
    {
        return $this->getData()->chained->parent->getComponent()->getPostsModel();
    }

    public function getPostsDirectory()
    {
        return $this->getData()->chained->parent->getComponent()->getPostsDirectory();
    }
}
