<?php
class Kwc_Basic_Svg_Model extends Kwf_Model_Db_Proxy
{
    protected $_table = 'kwc_basic_svgs';

    protected $_referenceMap = array(
        'Upload' => array(
            'column' => 'kwf_upload_id',
            'refModelClass' => 'Kwf_Uploads_Model',
        )
    );

    protected function _init()
    {
        parent::_init();
        $this->_exprs['filename'] = new Kwf_Model_Select_Expr_Parent('Upload', 'filename');
    }
}
