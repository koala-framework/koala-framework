<?php
class Kwf_Controller_Action_Cli_Web_SetupController extends Kwf_Controller_Action_Cli_Abstract
{
    public static function getHelp()
    {
        return "initial setup";
    }

    public function indexAction()
    {

        if (file_exists('update')) {
            throw new Kwf_Exception_Client("Application seems to be set up already. (update file exists)");
        }

        try {
            Kwf_Registry::get('db');
        } catch (Exception $e) {
            throw new Kwf_Exception_Client("Connection to database failed: ".$e->getMessage());
        }

        if (!Kwf_Registry::get('db')) {
            throw new Kwf_Exception_Client("Database not configured: create a config.local.ini containing database connection.");
        }

        try {
            Kwf_Registry::get('db')->query("SELECT 1");
        } catch (Exception $e) {
            throw new Kwf_Exception_Client("Connection to database failed: ".$e->getMessage());
        }

        try {
            $tables = Kwf_Registry::get('db')->fetchCol("SHOW TABLES");
        } catch (Exception $e) {
            throw new Kwf_Exception_Client("Fetching Tables failed: ".$e->getMessage());
        }
        if (in_array('kwf_update', $tables)) {
            throw new Kwf_Exception_Client("Application seems to be set up already. (kwf_update table exists)");
        }
        if ($tables) {
            throw new Kwf_Exception_Client("Database not empty, incomplete kwf installation or other application already exists in this database.");
        }

        $updates = array();
        foreach (Kwf_Update::getUpdateTags() as $tag) {
            $file = KWF_PATH.'/setup/'.$tag.'.sql';
            if (file_exists($file)) {
                $update = new Kwf_Update_Sql(0, null);
                $update->sql = file_get_contents($file);
                $updates[] = $update;
            }
        }

        foreach ($updates as $update) {
            $update->update();
        }

        if (!file_exists('update')) copy(KWF_PATH.'/setup/update', 'update');

        Kwf_Controller_Action_Cli_Web_UpdateController::update();


        $file = 'setup/setup.sql'; //initial setup for web
        if (file_exists($file)) {
            $update = new Kwf_Update_Sql(0, null);
            $update->sql = file_get_contents($file);
            $update->update();
        }

        echo "\n\nSetup finished.\nThank you for using Koala Framework.\n";
        exit;
    }
}
