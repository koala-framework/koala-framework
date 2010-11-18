<?php
class Vps_User_BruteForceInsertController extends Vps_Controller_Action
{
    public function indexAction()
    {
        Vps_Registry::set('db', Vps_Test::getTestDb($this->_getParam('testDb')));

        $model = Vps_Model_Abstract::getInstance('Vps_User_BruteForceModel');

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
        Vps_Registry::set('db', Vps_Test::getTestDb($this->_getParam('testDb')));
        try {
            $model = Vps_Model_Abstract::getInstance('Vps_User_BruteForceModel');
            $prefix = $this->_getParam('prefix');
            $row = $model->createUserRow($prefix.'@vivid-planet.com');
            $row->title = '';
            $row->firstname = 'n';
            $row->lastname = 's';

            $row2 = $model->createUserRow($prefix.'1@vivid-planet.com');
            $row2->title = '';
            $row2->firstname = 'm';
            $row2->lastname = 'h';
            $row2->save();

            $row->save();

        } catch (Vps_Exception_Client $e) {
            echo "0";
            exit;
        }
        echo $row->id;
        exit;
    }
}
