<?php
class Vpc_Abstract_List_OwnModel extends Vps_Component_FieldModel
{
    protected $_dependentModels = array(
        'Children' => 'Vpc_Abstract_List_Model'
    );
}
