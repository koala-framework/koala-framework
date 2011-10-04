<?php
class Vpc_TextImage_Text_TestStylesModel extends Vpc_Basic_Text_StylesModel
{
    public function __construct($config = array())
    {
        $config['proxyModel'] = new Vps_Model_FnF(array(
                'columns' => array('id', 'pos', 'name', 'tag', 'ownStyles', 'styles'),
                'data'=> array(
                )
            ));
        parent::__construct($config);
    }
    public static function getMasterStyles()
    {
        return array();
    }
}
