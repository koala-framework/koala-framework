<?php
class Vps_User_BruteForceInsertController extends Vps_Controller_Action
{
    public function indexAction()
    {
        $model = Vps_Registry::get('userModel');

        $prefix = uniqid('usr');
        for($i=0;$i<10;$i++) {
            $model->synchronize(Vps_Model_MirrorCache::SYNC_ALWAYS);
            $row = $model->createUserRow($prefix.'.'.$i.'@vivid-planet.com');
            $row->title = '';
            $row->firstname = 'n';
            $row->lastname = 's';
            $row->save();
            echo ".";
        }
        echo 'OK';
        exit;
    }

    public function createOneUserAction()
    {
        $model = Vps_Registry::get('userModel');
        $prefix = $this->_getParam('prefix');
        $row = $model->createUserRow($prefix.'@vivid-planet.com');
        $row->title = '';
        $row->firstname = 'n';
        $row->lastname = 's';
        try {
            $row->save();
        } catch (Vps_ClientException $e) {
            echo "0";
            exit;
        }
        echo $row->id;
        exit;
    }
}
