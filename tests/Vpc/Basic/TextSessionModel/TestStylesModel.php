<?php
class Vpc_Basic_TextSessionModel_TestStylesModel extends Vpc_Basic_Text_StylesModel
{
    public function __construct($config = array())
    {
        $config['proxyModel'] = new Vps_Model_Session(array(
                'namespace' => 'TextSessionModel_TestStylesModel',
                'uniqueIdentifier' => 'Vpc_Basic_Text_TestStylesModel_Proxy',
                'columns' => array('id', 'pos', 'name', 'tag', 'ownStyles', 'styles'),
                'defaultData'=> array(
                    array('id'=>1, 'pos'=>1, 'name'=>'Test1', 'tag'=>'h1', 'ownStyles'=>'', 'styles'=>serialize(array('font_weight'=>'bold', 'font_size'=>'10', 'text_align'=>'center'))),
                    array('id'=>2, 'pos'=>2, 'name'=>'Test2', 'tag'=>'p', 'ownStyles'=>'', 'styles'=>serialize(array('font_size'=>'10', 'color'=>'ff0000'))),
                    array('id'=>3, 'pos'=>3, 'name'=>'Test3', 'tag'=>'span', 'ownStyles'=>'', 'styles'=>serialize(array('font_size'=>'8', 'color'=>'00ff00'))),
                )
            ));
        parent::__construct($config);
    }

    public static function getMasterStyles()
    {
        return array();
    }
}
