<?php
class Vps_Form_MultiCheckbox_DataModel extends Vps_Form_MultiCheckbox_DataModelNoRel
{
    protected $_dependentModels = array(
        'Relation' => 'Vps_Form_MultiCheckbox_RelationModel'
    );
}
