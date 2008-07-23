<?php
class Vps_Controller_Action_Welcome_ContentController extends Vps_Controller_Action
{
    public function indexAction()
    {
        $this->view->content = '';
        try {
            $t = new Vps_Dao_Welcome();
            $row = $t->find(1)->current();
            if ($row) {
                $this->view->content = $row->content;
            }
        } catch (Zend_Db_Statement_Exception $e) {
            //wenn tabelle nicht existiert fehler abfangen
            $row = null;
        }
        if ($row) {
            $this->view->image = $row->getFileUrl('WelcomeImage', 'welcome');
            $this->view->imageSize = $row->getImageDimensions('WelcomeImage', 'welcome');
        } else {
            $this->view->image = false;
        }
        $this->view->application = Zend_Registry::get('config')->application;
        
        $this->_helper->viewRenderer->setRender('Welcome');
    }
}
