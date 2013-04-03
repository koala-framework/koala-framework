<?php
class Kwc_Trl_Table_Table_MasterModel extends Kwf_Model_FnF
{
    protected $_referenceMap = array(
        'table' => array(
            'column' => 'component_id',
            'refModelClass' => 'Kwc_Trl_Table_Table_OwnModel'
        )
    );

    protected function _init()
    {
        $this->_siblingModels[] = new Kwf_Model_Field(array('fieldName'=>'data'));
        parent::_init();
    }

    public function __construct($config = array())
    {
        $config['columns'] = array('id', 'component_id', 'data', 'pos', 'visible');
        $config['namespace'] = 'table_trl_model';
        $config['primaryKey'] = 'id';
        $config['data'] = array(
            array('id'=>1, 'component_id'=>'root-master_table', 'pos'=>0, 'visible'=>1, 'data'=>'{"css_style":null,"column1":"Abc","column2":"1234","column3":"234","column4":"","column5":"","column6":"","column7":"","column8":"","column9":"","column10":"","column11":"","column12":"","column13":"","column14":"","column15":"","column16":"","column17":"","column18":"","column19":"","column20":"","column21":"","column22":"","column23":"","column24":"","column25":"","column26":""}'),
            array('id'=>2, 'component_id'=>'root-master_table', 'pos'=>1, 'visible'=>1, 'data'=>'{"css_style":null,"column1":"Cde","column2":"4321","column3":"1234","column4":"","column5":"","column6":"","column7":"","column8":"","column9":"","column10":"","column11":"","column12":"","column13":"","column14":"","column15":"","column16":"","column17":"","column18":"","column19":"","column20":"","column21":"","column22":"","column23":"","column24":"","column25":"","column26":""}')
        );
        parent::__construct($config);
    }
}
