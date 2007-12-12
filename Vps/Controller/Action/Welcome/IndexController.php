<?php
class Vps_Controller_Action_Welcome_IndexController  extends Vps_Controller_Action
{
    public function indexAction()
    {
        try {
            $t = new Vps_Dao_Welcome();
            $row = $t->find(1)->current();
            $this->view->content = $row->content;
            $file = $row->findParentRow('Vps_Dao_File');
        } catch (Zend_Db_Statement_Exception $e) {
            //wenn tabelle nicht existiert fehler abfangen
            $file = null;
            $this->view->content = '';
        }
        if ($file) {
            $this->view->image = '/vps/welcome/media';
            $s = Vps_Media_Image::calculateScaleDimensions($file->getFileSource(),
                                                            array(300, 100));
            $this->view->imageSize = $s;
        } else {
            $this->view->image = false;
        }
        $this->view->application = Zend_Registry::get('config')->application;
        $this->view->setRenderFile('Welcome.html');
    }
}
