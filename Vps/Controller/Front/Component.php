<?php
class Vps_Controller_Front_Component extends Vps_Controller_Front
{
    protected function _init()
    {
        parent::_init();
        $this->addControllerDirectory(VPS_PATH . '/Vps/Controller/Action/Component',
                                        'vps_controller_action_component');

    }

    public function getRouter()
    {
        if (null == $this->_router) {
            if (php_sapi_name() == 'cli') {
                $this->setRouter(new Vps_Controller_Router_Cli());
            } else {
                $this->setRouter(new Vps_Controller_Router_Component());
            }
        }
        return $this->_router;
    }

    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
            self::$_instance->_init();
        }

        return self::$_instance;
    }
}
