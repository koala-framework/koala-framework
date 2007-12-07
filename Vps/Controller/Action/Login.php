<?php
class Vps_Controller_Action_Login extends Vps_Controller_Action_User_Login
{
    public function headerAction()
    {
        try {
            $t = new Vps_Dao_Welcome();
            $row = $t->find(1)->current();
            $file = $row->findParentRow('Vps_Dao_File');
        } catch (Zend_Db_Statement_Exception $e) {
            //wenn tabelle nicht existiert fehler abfangen
            $file = null;
        }
        if ($file) {
            $this->view->image = '/vps/loginmedia';
            $s = Vps_Media_Image::calculateScaleDimensions($file->getFileSource(),
                                                            array(300, 50));
            $this->view->imageSize = $s;
        } else {
            $this->view->image = false;
        }
        $this->view->application = Zend_Registry::get('config')->application;
        $this->view->setRenderFile('LoginHeader.html');
    }
}
