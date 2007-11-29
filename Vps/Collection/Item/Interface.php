<?php
interface Vps_Collection_Item_Interface
{
    public function hasChildren();
    public function getChildren();
    public function getByName($name);
    public function getName();
}
