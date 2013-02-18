<?php
class Kwc_Articles_Category_Directory_CategoriesController extends Kwc_Directories_Category_Directory_CategoriesController
{
    protected  function _getCategoryDirectory()
    {
        $c = Kwf_Component_Data_Root::getInstance()
            ->getComponentByClass('Kwc_Articles_Category_Directory_Component', array('ignoreVisible'=>true));
        return $c;
    }
}
