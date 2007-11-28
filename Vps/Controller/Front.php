<?ph
Zend_Controller_Action_HelperBroker::removeHelper('viewRenderer')
Zend_Controller_Action_HelperBroker::addHelper(new Vps_Controller_Action_Helper_ViewRenderer())

class Vps_Controller_Front extends Zend_Controller_Fron

    protected function _init(
    
        $this->setControllerDirectory('application/controllers')
        $this->returnResponse(true)
    
    public static function getInstance(
    
        if (null === self::$_instance) 
            self::$_instance = new self()
            self::$_instance->_init()
        

        return self::$_instance
    
