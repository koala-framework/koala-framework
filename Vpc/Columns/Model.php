<?php
class Vpc_Columns_Model extends Vps_Component_FieldModel
{
    protected $_dependentModels = array(
        'Columns' => 'Vpc_Columns_ColumnsModel'
    );
}
