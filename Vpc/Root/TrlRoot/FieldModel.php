<?php
class Vpc_Root_TrlRoot_FieldModel extends Vps_Component_FieldModel
{
    protected $_referenceMap = array(
        'sibling' => array(
            'refModelClass' => 'Vpc_Root_TrlRoot_Model',
            'column' => 'component_id'
        )
    );
}
