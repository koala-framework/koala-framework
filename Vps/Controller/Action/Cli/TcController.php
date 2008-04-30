<?php
class Vps_Controller_Action_Cli_TcController extends Vps_Controller_Action
{
    public function indexAction()
    {
        $debug = $this->_getParam('debug');
        if ($debug) {
            $writer = new Zend_Log_Writer_Stream('php://output');
            $writer->setFormatter(new Vps_Log_Formatter_Console());
            $logger = new Zend_Log($writer);
            Zend_Registry::set('debugLogger', $logger);

            Zend_Registry::get('db')->getProfiler()->setEnabled(false);
        
            $db = Zend_Registry::get('db');
            $db->query("DROP TABLE IF EXISTS vps_tree_cache_copy");
            $db->getConnection()->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
            $createTable = $db->fetchRow('SHOW CREATE TABLE vps_tree_cache');
            $db->getConnection()->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $createTable = $createTable['Create Table'];
            $createTable = str_replace('vps_tree_cache', 'vps_tree_cache_copy', $createTable);
            $db->query($createTable);
            $db->query("INSERT INTO vps_tree_cache_copy SELECT * FROM vps_tree_cache");
        }

        set_time_limit(10);
        $start = microtime(true);
        $t = new Vps_Dao_TreeCache();
        $t->regenerate();
        echo "\nTreeCache erfolgreich erstellt in ".(microtime(true)-$start)."sec\n";

        if ($debug) {
            echo "\n\nRows neu: ".$db->fetchOne("SELECT COUNT(*) FROM vps_tree_cache")."\n";
            echo "Rows alt: ".$db->fetchOne("SELECT COUNT(*) FROM vps_tree_cache_copy")."\n";
        }
        //todo: response-object?
        exit;
    }
}
