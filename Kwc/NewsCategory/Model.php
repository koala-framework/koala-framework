<?php
class Vpc_NewsCategory_Model extends Vpc_News_Directory_Model
{
    protected $_dependentModels = array(
        'Categories' => 'Vpc_News_Category_Directory_NewsToCategoriesModel'
    );
}
