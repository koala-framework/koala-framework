<?php
class Kwc_Events_Category_Directory_Model extends Kwc_Events_Directory_Model
{
    protected $_dependentModels = array(
        'Categories' => 'Kwc_Events_Category_Directory_EventsToCategoriesModel'
    );
}
