<?php
class Kwc_Tags_ComponentModel extends Kwf_Model_FnF
{
    protected $_primaryKey = 'component_id';

    protected $_dependentModels = array(
        'ComponentToTag' => 'Kwc_Tags_ComponentToTag'
    );
}
