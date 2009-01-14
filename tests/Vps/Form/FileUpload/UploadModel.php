<?php
class Vps_Form_FileUpload_UploadModel extends Vps_Model_FnF
{
    protected $_namespace = 'Vps_Form_FileUpload_FooModel';
    protected $_primaryKey = 'test_id';
    protected $_defaultData = array(
        array('id' => 3, 'filename' => 'test'),
        array('id' => 4, 'filename' => 'test')
    );
}