<?php
class Kwc_Guestbook_Admin extends Kwc_Directories_Item_Directory_Admin
{
    public function addResources(Kwf_Acl $acl)
    {
        parent::addResources($acl);
        $this->_addResourcesBySameClass($acl);
    }
}