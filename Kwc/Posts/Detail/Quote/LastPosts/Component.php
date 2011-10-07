<?php
class Kwc_Posts_Detail_Quote_LastPosts_Component extends Kwc_Posts_Write_LastPosts_Component
{
    protected function _getItemDirectory()
    {
        return $this->getData()->parent->parent->parent->parent;
    }
}
