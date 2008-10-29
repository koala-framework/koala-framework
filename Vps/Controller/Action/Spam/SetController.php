<?php
class Vps_Controller_Action_Spam_SetController extends Vps_Controller_Action
{
    public function indexAction()
    {
        // wenn zuvor als Spam (-verdacht) markiert und dann als ham reinkommt,
        // ham setzen und email doch schicken
        $value = $this->_getParam('value');
        if ($value == 0) {
            $row = Vps_Model_Abstract::getInstance('Vps_Model_Mail')->getRow($this->_getParam('id'));
            if (!$row) die('0');
            $row->is_spam = 0;
            $row->save();
            $result = $row->sendMail();
            if (!$result) die('0');
            die('1');
/*
            $model = new Vps_Model_Db(array('table' => new Vps_Model_Mail_Table()));
            $row = $model->find($this->_getParam('id'))->current();

            if ($row->is_spam && !$row->mail_sent) {
                $varsModel = new Vps_Model_Field(array(
                    'fieldName'   => 'serialized_mail_vars',
                    'parentModel' => $model
                ));
                $varsRow = $varsModel->getRowByParentRow($row);

                $essentialModel = new Vps_Model_Field(array(
                    'fieldName'   => 'serialized_mail_essentials',
                    'parentModel' => $model
                ));
                $essentialRow = $essentialModel->getRowByParentRow($row);

                if (Vps_Model_Mail_Row::getSpamKey($row) != $this->_getParam('key')) {
                    die('0');
                }

                $row->is_spam = 0;
                $row->mail_sent = 1;
                $row->save();

                Vps_Model_Mail_Row::sendMail($essentialRow, $varsRow);

                die('1');
            }
*/
        }
        die('0');
    }


}
