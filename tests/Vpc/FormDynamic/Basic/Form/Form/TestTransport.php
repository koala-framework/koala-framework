<?php
/**
 * NOOP Transport für Test. Weil am vivid-test-server funktioniert das Mail schicken nicht, und
 * es wär eh sinnlos zu machen da es nicht getestet wird.
 */
class Vpc_FormDynamic_Basic_Form_Form_TestTransport extends Zend_Mail_Transport_Abstract
{
    //does nothing...
    protected function _sendMail()
    {
        return true;
    }
}