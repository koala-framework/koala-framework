<?php
class Vps_Component_Generator_GetRecursiveChildComponentsPerformance_Table2Model extends Vps_Model_FnF
{
    protected $_columns = array('id', 'name', 'component', 'component_id');
    protected $_data = array(
                array('id'=>1, 'name'=>'foo1', 'component'=>'table3', 'component_id'=>'root_table1-1'),
                array('id'=>2, 'name'=>'foo3', 'component'=>'empty', 'component_id'=>'root_table1-2'),
                array('id'=>3, 'name'=>'foo4', 'component'=>'empty', 'component_id'=>'root_table2-4'),
                array('id'=>4, 'name'=>'foo5', 'component'=>'empty', 'component_id'=>'root_table2-4')
            );
}
