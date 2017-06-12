<?php
class Kwf_Controller_Action_Welcome_ContentController extends Kwf_Controller_Action
{
    public function indexAction()
    {
        $this->view->content = '';
        try {
            $t = new Kwf_Util_Model_Welcome();
            $row = $t->getRow(1);
            if ($row) {
                $this->view->content = $row->content;
            }
        } catch (Zend_Db_Statement_Exception $e) {
            //wenn tabelle nicht existiert fehler abfangen
            $row = null;
        }
        if ($row && $row->getParentRow('WelcomeImage')) {
            $this->view->image = Kwf_Media::getUrlByRow(
                $row, 'WelcomeImage'
            );
            $this->view->imageSize = Kwf_Media::getDimensionsByRow($row, 'WelcomeImage');
        } else {
            $this->view->image = false;
        }

        $this->view->application = Zend_Registry::get('config')->application;
        if (Kwf_Registry::get('userModel')->getAuthedUserRole() != 'admin') {
            $this->view->application->kwf->version = null;
        }

        $this->_helper->viewRenderer->setRender('Welcome');
    }
}
