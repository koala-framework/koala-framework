<?php
class Vpc_Guestbook_Admin extends Vpc_Directories_Item_Directory_Admin
{
    public function addResources(Vps_Acl $acl)
    {
        parent::addResources($acl);
        $this->_addResourcesBySameClass($acl);
    }
}