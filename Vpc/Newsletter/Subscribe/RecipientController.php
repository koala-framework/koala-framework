<?php
class Vpc_Newsletter_Subscribe_RecipientController extends Vps_Controller_Action_Auto_Form
{
    protected $_permissions = array('save', 'add');
    protected $_formName = 'Vpc_Newsletter_EditSubscriber_Form';

    protected function _beforeInsert(Vps_Model_Row_Interface $row)
    {
        $row->subscribe_date = date('Y-m-d H:i:s');
        if ($row->getModel()->hasColumn('activated')) {
            $row->activated = 1;
        }
    }
}
