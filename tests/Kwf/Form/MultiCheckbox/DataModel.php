<?php
class Kwf_Form_MultiCheckbox_DataModel extends Kwf_Form_MultiCheckbox_DataModelNoRel
{
    protected $_dependentModels = array(
        'Relation' => 'Kwf_Form_MultiCheckbox_RelationModel'
    );
}
