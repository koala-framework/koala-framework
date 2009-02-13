<?php
class Vpc_Advanced_Amazon_Nodes_ProductsDirectory_View_Component extends Vpc_Directories_List_ViewPage_Component
{
    public function getViewCacheLifetime()
    {
        return 24*60*60;
    }
}
