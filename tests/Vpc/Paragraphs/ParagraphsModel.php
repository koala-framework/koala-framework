<?php
class Vpc_Paragraphs_ParagraphsModel extends Vpc_Paragraphs_Model
{
    public function __construct($config = array())
    {
        $config['proxyModel'] = new Vps_Model_FnF(array(
            'columns' => array('id', 'component_id', 'pos', 'visible', 'component'),
            'primaryKey' => 'id',
            'data'=> array(
                array('id' => 1, 'component_id'=>'root', 'pos'=>1, 'visible' => 1, 'component' => 'paragraph'),
                array('id' => 2, 'component_id'=>'root', 'pos'=>2, 'visible' => 0, 'component' => 'paragraph'),
                array('id' => 11, 'component_id'=>'foo', 'pos'=>1, 'visible' => 1, 'component' => 'paragraph')
            )
        ));
        parent::__construct($config);
    }
}
