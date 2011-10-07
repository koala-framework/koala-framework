<?php
class Kwf_Controller_Action_Cli_Web_CreateUsersController extends Kwf_Controller_Action_Cli_Abstract
{
    public static function getHelp()
    {
        return 'Creates users in the service from kwf_users table';
    }
    public static function getHelpOptions()
    {
        return array(
        );
    }
    public function indexAction()
    {
        $appId = Kwf_Registry::get('config')->application->id;

        $webModel = new Kwf_User_Web_Model();
        $amount = $webModel->countRows();
        $relationModel = new Kwf_User_Relation_Model();
        $rows = $webModel->getRows();
        $i=1;
        foreach ($rows as $row) {
            $exists = $relationModel->getRow(
                $relationModel->select()
                    ->whereEquals('user_id', $row->id)
                    ->whereEquals('web_id', $appId)
            );
            if (!$exists) {
                $nr = $relationModel->createRow();
                $nr->user_id = $row->id;
                $nr->web_id = $appId;
                $nr->save();
                echo "[".($i++)."/$amount] Hinzugefuegt (Id: ".$row->id.")\n";
            } else {
                echo "[".($i++)."/$amount] Bereits vorhanden (Id: ".$row->id.")\n";
            }
        }
        echo "\nfertig, service up to date.\n\n";
        
        Kwf_Util_ClearCache::getInstance()->clearCache('cache_users', true);
        exit;
    }
}
