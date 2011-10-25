<?php
class Kwc_Advanced_Amazon_Nodes_ProductsDirectory_View_Component extends Kwc_Directories_List_ViewPage_Component
{
    public function getViewCacheLifetime()
    {
        return 24*60*60;
    }
}
