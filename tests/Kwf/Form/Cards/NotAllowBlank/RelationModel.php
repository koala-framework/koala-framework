<?php
class Vps_Form_Cards_NotAllowBlank_RelationModel extends Vps_Model_Session
{
    protected $_namespace = 'Vps_Form_Cards_NotAllowBlank_RelationModel';
    protected $_columns = array('id', 'model_id', 'data_id');

    protected $_referenceMap = array(
        'Model' => array(
            'column' => 'model_id',
            'refModelClass' => 'Vps_Form_Cards_NotAllowBlank_Model'
        )
    );
    protected $_defaultData = array(
        array('id' => 1, 'model_id' => 1, 'data_id' => 1),
        array('id' => 2, 'model_id' => 2, 'data_id' => 2),
        array('id' => 3, 'model_id' => 3, 'data_id' => 3),
        array('id' => 4, 'model_id' => 4, 'data_id' => 4)
    );
}