<?php
class Vps_User_ServiceConnController extends Vps_Controller_Action
{
    public function indexAction()
    {
        $m = Vps_Model_Abstract::getInstance('Vps_User_All_Model');
        $s = $m->select();

        for($i=0;$i<10;$i++) {
            $r = $m->getRow($s);
            echo '.';
        }
        echo 'OK';
        exit;
    }
}
