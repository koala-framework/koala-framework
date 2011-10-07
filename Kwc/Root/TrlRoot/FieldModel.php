<?php
class Kwc_Root_TrlRoot_FieldModel extends Kwf_Component_FieldModel
{
    protected $_referenceMap = array(
        'sibling' => array(
            'refModelClass' => 'Kwc_Root_TrlRoot_Model',
            'column' => 'component_id'
        )
    );
}
