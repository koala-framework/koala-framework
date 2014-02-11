<?php
class Kwc_ImageResponsive_CreatesImgElement_Components_TextImage_ImageEnlarge_LinkTag_Model extends Kwc_TextImage_ImageEnlarge_LinkTag_Model
{
    public function __construct($config = array())
    {
        $config['proxyModel'] = new Kwf_Model_FnF(array(
            'primaryKey' => 'component_id',
            'data'=> array(
                array('component_id'=>'root_textimage1-image-linkTag', 'component'=>'enlarge')
            )
        ));
        parent::__construct($config);
    }
}
