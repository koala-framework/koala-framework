<?php
class Kwc_Posts_Detail_Quote_LastPosts_Component extends Kwc_Posts_Write_LastPosts_Component
{
    public static function getItemDirectoryClasses($directoryClass)
    {
        return self::_getParentItemDirectoryClasses($directoryClass, 3);
    }

    protected function _getItemDirectory()
    {
        return $this->getData()->parent->parent->parent->parent;
    }
}
