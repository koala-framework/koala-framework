<?php
class Kwf_Component_Generator_StaticSelectChildTablePage_Banner2_Model extends Kwf_Model_FnF
{
    protected $_toStringField = 'id';
    protected $_data = array(
        array('id' => 1, 'component_id' => 'root_page1-banner', 'visible'=>true), //should not be visible as banner1 is used
        array('id' => 2, 'component_id' => 'root_page2-banner', 'visible'=>true),
    );
}
