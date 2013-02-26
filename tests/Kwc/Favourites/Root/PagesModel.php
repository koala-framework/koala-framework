<?php
class Kwc_Favourites_Root_PagesModel extends Kwf_Model_Session
{
    protected $_data = array(
        array('id'=>2000, 'pos'=>1, 'visible'=>true, 'name'=>'Home', 'filename' => 'home',
              'parent_id'=>'root', 'component'=>'paragraphs', 'is_home'=>true, 'category' =>'main', 'hide'=>false),
        array('id'=>2001, 'pos'=>2, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo',
              'parent_id'=>'root', 'component'=>'favouritesParentStatic', 'is_home'=>false, 'category' =>'main', 'hide'=>false),
        array('id'=>2002, 'pos'=>3, 'visible'=>true, 'name'=>'Bar3', 'filename' => 'bar3',
              'parent_id'=>'root', 'component'=>'favourites', 'is_home'=>false, 'category' =>'main', 'hide'=>false),
        array('id'=>2003, 'pos'=>4, 'visible'=>true, 'name'=>'Bar4', 'filename' => 'bar4',
              'parent_id'=>'root', 'component'=>'favourites', 'is_home'=>false, 'category' =>'main', 'hide'=>false),
        array('id'=>2004, 'pos'=>5, 'visible'=>true, 'name'=>'Bar5', 'filename' => 'bar5',
              'parent_id'=>'root', 'component'=>'favouritesPage', 'is_home'=>false, 'category' =>'main', 'hide'=>false),
        array('id'=>2005, 'pos'=>6, 'visible'=>true, 'name'=>'Bar6', 'filename' => 'bar6',
              'parent_id'=>'root', 'component'=>'favouritesBox', 'is_home'=>false, 'category' =>'main', 'hide'=>false),
        array('id'=>2006, 'pos'=>7, 'visible'=>true, 'name'=>'selenium', 'filename' => 'selenium',
              'parent_id'=>'root', 'component'=>'selenium', 'is_home'=>false, 'category' =>'main', 'hide'=>false),
    );
}
