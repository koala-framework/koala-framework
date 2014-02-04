<?php
class Kwc_ImageResponsive_Components_TextImage_Text_StylesModel extends Kwc_Basic_Text_StylesModel
{
    public function __construct($config = array())
    {
        $config['proxyModel'] = new Kwf_Model_FnF(array(
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
