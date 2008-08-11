<?php
class Vpc_Posts_Post_Quote_Form_Component extends Vpc_Posts_Write_Form_Component
{
    protected function _getPostsComponent()
    {
        return $this->getData()->parent->parent->parent;
    }
}
