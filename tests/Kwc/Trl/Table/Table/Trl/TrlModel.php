<?php
class Kwc_Trl_Table_Table_Trl_TrlModel extends Kwf_Model_FnF
{
    protected function _init()
    {
        $this->_siblingModels[] = new Kwf_Model_Field(array('fieldName'=>'data'));
        parent::_init();
    }

    public function __construct($config = array())
    {
        $config['columns'] = array('id', 'component_id', 'data', 'master_id');
        $config['namespace'] = 'table_trl_model';
        $config['primaryKey'] = 'id';
        $config['data'] = array(
            array('id'=>1, 'component_id'=>'root-en_table', 'data'=>'{"css_style":null,"column1":"Abc","column3":"234","column4":"","column5":"","column6":"","column7":"","column8":"","column9":"","column10":"","column11":"","column12":"","column13":"","column14":"","column15":"","column16":"","column17":"","column18":"","column19":"","column20":"","column21":"","column22":"","column23":"","column24":"","column25":"","column26":""}', 'master_id'=>2),
        );
        parent::__construct($config);
    }
}
