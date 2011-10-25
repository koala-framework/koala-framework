<?php
class Kwc_NewsCategory_Model extends Kwc_News_Directory_Model
{
    protected $_dependentModels = array(
        'Categories' => 'Kwc_News_Category_Directory_NewsToCategoriesModel'
    );
}
