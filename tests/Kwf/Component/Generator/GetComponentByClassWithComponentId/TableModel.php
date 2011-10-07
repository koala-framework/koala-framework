<?php
class Vps_Component_Generator_GetComponentByClassWithComponentId_TableModel extends Vps_Model_FnF
{
    protected $_columns = array('id', 'name', 'component_id');
    protected $_data = array(
                array('id'=>1, 'name'=>'foo1', 'component_id'=>'root-foo1'),
                array('id'=>2, 'name'=>'foo2', 'component_id'=>'root-foo1'),
                array('id'=>3, 'name'=>'foo3', 'component_id'=>'root-foo2')
            );
}
