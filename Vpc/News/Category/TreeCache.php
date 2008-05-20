<?php
class Vpc_News_Category_TreeCache extends Vpc_TreeCache_StaticPage
{
    protected $_classes = array(array(
        'componentClass' => 'Vpc_News_Category_Directory_Component',
        'name' => 'Categories',
        'id' => 'categories'
    ));

    protected function _select()
    {
        $select = parent::_select();
        $select->where('menu = 1');
        return $select;
    }

}
