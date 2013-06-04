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
            //for pre 3.3 webs, update file got replaced by kwf_update table
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
        foreach (Kwf_Util_Update_Helper::getUpdateTags() as $tag) {
            $file = KWF_PATH.'/setup/'.$tag.'.sql';
            if (file_exists($file)) {
                $update = new Kwf_Update_Sql(0, null);
                $update->sql = file_get_contents($file);
                $updates[] = $update;
            }
        }

        $updates = array_merge($updates, Kwf_Util_Update_Helper::getUpdates(0, 9999999));

        $file = 'setup/setup.sql'; //initial setup for web
        if (file_exists($file)) {
            $update = new Kwf_Update_Sql(0, null);
            $update->sql = file_get_contents($file);
            $updates[] = $update;
        }

        $c = new Zend_ProgressBar_Adapter_Console();
        $c->setElements(array(Zend_ProgressBar_Adapter_Console::ELEMENT_PERCENT,
                                Zend_ProgressBar_Adapter_Console::ELEMENT_BAR,
                                Zend_ProgressBar_Adapter_Console::ELEMENT_TEXT));
        $c->setTextWidth(50);

        $runner = new Kwf_Util_Update_Runner($updates);
        $progress = new Zend_ProgressBar($c, 0, $runner->getProgressSteps());
        $runner->setProgressBar($progress);
        if (!$runner->checkUpdatesSettings()) {
            echo "\ncheckSettings failed, setup stopped\n";
            exit;
        }
        $doneNames = $runner->executeUpdates();
        $runner->writeExecutedUpdates($doneNames);

        $errors = $runner->getErrors();
        if ($errors) {
            echo "\n\n================\n";
            echo count($errors)." setup script(s) failed:\n";
            foreach ($errors as $error) {
                echo $error['name'].": \n";
                echo $error['message']."\n\n";
            }
        } else {
            echo "\n\nSetup finished.\nThank you for using Koala Framework.\n";
        }
        exit;
    }
}
