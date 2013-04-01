<?php
class Kwf_Controller_Action_Maintenance_SetupController extends Kwf_Controller_Action
{
    public function preDispatch()
    {
        parent::preDispatch();
        //TODO check if not already installed, like in Cli/SetupController
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
        //TODO add progress bar
        //TODO add "warning" response
        //TODO check for config.local.ini being writeable
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


        //TODO add progress bar

        $updates = array();
        foreach (Kwf_Util_Update_Helper::getUpdateTags() as $tag) {
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

        $updates = array_merge($updates, Kwf_Util_Update_Helper::getUpdates(0, 9999999));

        $file = 'setup/setup.sql'; //initial setup for web
        if (file_exists($file)) {
            $update = new Kwf_Update_Sql(0, null);
            $update->sql = file_get_contents($file);
            $updates[] = $update;
        }

        $runner = new Kwf_Util_Update_Runner($updates);
        $doneNames = $runner->executeUpdates();
        $runner->writeExecutedUpdates($doneNames);
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
    }
}
