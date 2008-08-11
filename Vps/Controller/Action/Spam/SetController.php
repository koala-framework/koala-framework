<?php
class Vps_Controller_Action_Spam_SetController extends Vps_Controller_Action
{
    public function indexAction()
    {
        // wenn zuvor als Spam (-verdacht) markiert und dann als ham reinkommt,
        // ham setzen und email doch schicken
        $value = $this->_getParam('value');
        if ($value == 0) {
            $model = new Vps_Model_Mail();
            $row = $model->find($this->_getParam('id'))->current();

            if ($row->is_spam && !$row->mail_sent) {
                if ($row->getSpamKey() != $this->_getParam('key')) {
                    die('0');
                }

                $row->is_spam = 0;
                $row->save(); // mit diesem save wird die email mitgesendet

                die('1');
            }
        }
        die('0');
    }


}
