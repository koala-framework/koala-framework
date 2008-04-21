<?php
class Vps_Controller_Front_Component extends Vps_Controller_Front
{
    protected function _init()
    {
        $this->setDispatcher(new Vps_Controller_Dispatcher());

        $router = $this->getRouter();

        $router->AddRoute('admin', new Zend_Controller_Router_Route(
                    '/admin/:module/:controller/:action',
                    array('module'=>'vps_controller_action_component',
                          'controller' => 'index',
                          'action' => 'index')));
        $router->AddRoute('component', new Zend_Controller_Router_Route(
                    '/admin/component/:controller/:action',
                    array('module'=>'vps_controller_action_component',
                          'action' => 'index')));
        $router->AddRoute('components', new Zend_Controller_Router_Route(
                    '/admin/components/:action',
                    array('module' => 'vps_controller_action_component',
                          'controller' => 'components',
                          'action' => 'index')));
        $router->AddRoute('componentshow', new Zend_Controller_Router_Route(
                    '/admin/component/show/:class/:componentId',
                    array('componentId'=>null,
                          'module' => 'vps_controller_action_component',
                          'controller' => 'components',
                          'action' => 'show')));
        $router->AddRoute('componentjsonshow', new Zend_Controller_Router_Route(
                    '/admin/component/jsonshow/:class/:componentId',
                    array('componentId'=>null,
                          'module' => 'vps_controller_action_component',
                          'controller' => 'components',
                          'action' => 'jsonshow')));
        $router->AddRoute('componentedit', new Zend_Controller_Router_Route(
                    '/admin/component/edit/:class/:action',
                    array('module' => 'component',
                          'controller' => 'component',
                          'action' => 'index')));
        parent::_init();
        $this->addControllerDirectory(VPS_PATH . '/Vps/Controller/Action/Component',
                                        'vps_controller_action_component');

    }

    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
            self::$_instance->_init();
        }

        return self::$_instance;
    }
    public function dispatchVpc()
    {
        $uri = substr($_SERVER['REDIRECT_URL'], 1);
        $i = strpos($uri, '/');
        if ($i) $uri = substr($uri, 0, $i);
        if (!in_array($uri, array('media', 'vps', 'admin'))) {

            $uri = $_SERVER['REDIRECT_URL'];
            $pageCollection = Vps_PageCollection_Abstract::getInstance();
            try {
                $page = $pageCollection->getPageByPath($uri);
            } catch (Vpc_UrlNotFoundException $e) {
                header('Location: ' . $e->getMessage(), true, 301);
                die();
            }
            if (!$page) {
                throw new Vps_Controller_Action_Web_FileNotFoundException('Page not found for path ' . $uri);
            }

            $page->sendContent($page);

            exit;
        }
    }
}
