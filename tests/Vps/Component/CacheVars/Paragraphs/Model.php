<?php
class Vps_Component_CacheVars_Paragraphs_Model extends Vps_Model_FnF
{
    protected $_columns = array('id', 'component_id', 'component');
    protected $_data = array(
        array('id'=>1, 'component_id' => 'root-paragraphs', 'component' => 'empty'),
        array('id'=>2, 'component_id' => 'root-paragraphs', 'component' => 'custom')
    );
}
