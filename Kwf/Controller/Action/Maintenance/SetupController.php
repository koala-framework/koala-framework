<?php
class Kwf_Controller_Action_Maintenance_SetupController extends Kwf_Controller_Action
{
    public function preDispatch()
    {
        //don't call parent, no acl required

        if (file_exists('update')) {
            //for pre 3.3 webs, update file got replaced by kwf_update table
            throw new Kwf_Exception_Client("Application seems to be set up already. (update file exists)");
        }

        if (file_exists('config.local.ini') && filesize('config.local.ini') > 0) {
            throw new Kwf_Exception_Client("Application seems to be set up already. (config.local.ini file exists)");
        }

        $db = null;
        try {
            $db = Kwf_Registry::get('db');
        } catch (Exception $e) {
        }

        if ($db) {
            try {
                $tables = Kwf_Registry::get('db')->fetchCol("SHOW TABLES");
            } catch (Exception $e) {
                throw new Kwf_Exception_Client("Fetching Tables failed: ".$e->getMessage());
            }
            if (in_array('kwf_update', $tables)) {
                throw new Kwf_Exception_Client("Application seems to be set up already. (kwf_update table exists)");
            }
            if ($tables) {
                throw new Kwf_Exception_Client("Database not empty, kwf installation or other application already exists in this database.");
            }
        }
    }

    public function indexAction()
    {
        $this->view->kwfVersion = Kwf_Config::getValue('application.kwf.name') . ' ' . trlKwf('Version') . ' ' . Kwf_Config::getValue('application.kwf.version');
        $this->view->appVersion = Kwf_Config::getValue('application.name');
        $this->view->defaultDbNmae = Kwf_Config::getValue('application.id');
        $this->view->assetsType = 'Kwf_Controller_Action_Maintenance:Setup';
        $this->view->viewport = 'Kwf.Maintenance.Viewport';
        $this->view->xtype = 'kwf.maintenance.setup';
    }
    
    public function jsonCheckRequirementsAction()
    {
        //TODO add "warning" response (+plus additional info output)
        //TODO check for config.local.ini being writeable and does not yet exist (or is empty)
        //TODO check for web running in root of domain
        //TODO alternative for maintenance mode: current one needs write perm on bootstrap.php plus sucks across multiple servers
        $this->view->checks = Kwf_Util_Check_Config::getCheckResults();
    }

    public function jsonInstallAction()
    {
        $cfg = "[production]\n";
        $cfg .= "database.web.username = ".$this->_getParam('db_username')."\n";
        $cfg .= "database.web.password = ".$this->_getParam('db_password')."\n";
        $cfg .= "database.web.dbname = ".$this->_getParam('db_dbname')."\n";
        $cfg .= "database.web.host = ".$this->_getParam('db_host')."\n";
        $cfg .= "\n";
        $cfg .= "debug.error.log = ".(!$this->_getParam('display_errors') ? 'true' : 'false')."\n";
        file_put_contents('config.local.ini', $cfg);

        //re-create config to load changed config.local.ini
        Kwf_Config::deleteValueCache('database');
        Kwf_Config_Web::reload();
        Zend_Registry::getInstance()->offsetUnset('db');
        Zend_Registry::getInstance()->offsetSet('dao', new Kwf_Dao());

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

        $update = new Kwf_Update_Sql(0, null);
        $db = Kwf_Registry::get('db');
        mt_srand((double)microtime()*1000000);
        $passwordSalt = substr(md5(uniqid(mt_rand(), true)), 0, 10);
        $update->sql = "TRUNCATE TABLE kwf_users;\n";
        $update->sql .= "INSERT INTO kwf_users SET
            role='admin',
            email=".$db->quote($this->_getParam('admin_email')).",
            password=".$db->quote(md5($this->_getParam('admin_password').$passwordSalt)).",
            password_salt=".$db->quote($passwordSalt).";\n";
        $updates[] = $update;

        $c = new Kwf_Util_ProgressBar_Adapter_Cache($this->_getParam('progressNum'));

        $runner = new Kwf_Util_Update_Runner($updates);
        $progress = new Zend_ProgressBar($c, 0, $runner->getProgressSteps());
        $runner->setProgressBar($progress);
        if (!$runner->checkUpdatesSettings()) {
            throw new Kwf_Exception_Client("checkSettings failed, setup stopped");
        }
        $doneNames = $runner->executeUpdates();
        $runner->writeExecutedUpdates($doneNames);
 
        $errors = $runner->getErrors();
        if ($errors) {
            $errMsg = count($errors)." setup script(s) failed:\n";
            foreach ($errors as $error) {
                $errMsg .= $error['name'].": \n";
                $errMsg .= $error['message']."\n\n";
            }
            throw new Kwf_Exception_Client(nl2br($errMsg));
        }
    }

    public function jsonCheckDbAction()
    {
        $dbConfig = array(
            'username' => $this->_getParam('db_username'),
            'password' => $this->_getParam('db_password'),
            'dbname' => $this->_getParam('db_dbname'),
            'host' => $this->_getParam('db_host'),
        );
        try {
            $db = Zend_Db::factory('PDO_MYSQL', $dbConfig);
            $db->query('SET names UTF8');
        } catch (Exception $e) {
            throw new Kwf_Exception_Client($e->getMessage());
        }

        try {
            $tables = $db->fetchCol("SHOW TABLES");
        } catch (Exception $e) {
            throw new Kwf_Exception_Client("Fetching Tables failed: ".$e->getMessage());
        }
        if ($tables) {
            throw new Kwf_Exception_Client("Database not empty, incomplete kwf installation or other application already exists in this database.");
        }
    }
}
