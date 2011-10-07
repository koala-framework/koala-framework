<?php
class Vps_Controller_Action_Welcome_ContentController extends Vps_Controller_Action
{
    public function indexAction()
    {
        $this->view->content = '';
        try {
            $t = new Vps_Util_Model_Welcome();
            $row = $t->getRow(1);
            if ($row) {
                $this->view->content = $row->content;
            }
        } catch (Zend_Db_Statement_Exception $e) {
            //wenn tabelle nicht existiert fehler abfangen
            $row = null;
        }
        if ($row) {
            $this->view->image = Vps_Media::getUrlByRow(
                $row, 'WelcomeImage'
            );
            $this->view->imageSize = Vps_Media::getDimensionsByRow($row, 'WelcomeImage');
        } else {
            $this->view->image = false;
        }

        $this->view->application = Zend_Registry::get('config')->application;

        $this->_helper->viewRenderer->setRender('Welcome');
    }
}
