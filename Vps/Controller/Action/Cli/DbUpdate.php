<?php

abstract class Vps_Controller_Action_Cli_DbUpdate extends Vps_Controller_Action_Cli_Abstract
{
    protected $_db = null;

    public static function getHelp()
    {
        return "Updates the Database";
    }

    public static function getHelpOptions()
    {
        return array();
    }

    public function indexAction()
    {
        $this->_db = Vps_Registry::get('db');

        echo "Lösche Cache. Befehl: rm -rf ".dirname(__FILE__)."/../../cache/*/*\n";
        system('rm -rf '.dirname(__FILE__).'/../../cache/*/*');

        $this->_upgrade();

        echo "Lösche Cache. Befehl: rm -rf ".dirname(__FILE__)."/../../cache/*/*\n";
        system('rm -rf '.dirname(__FILE__).'/../../cache/*/*');

        $this->_helper->viewRenderer->setNoRender(true);
    }

    /**
     * Used to Upgrade from a later revision
     */
    abstract protected function _upgrade();

    protected function _textComponents()
    {
        $start = microtime(true);
        $existingCount = $addedCount = $deletedCount = 0;
        $t = new Vpc_Basic_Text_Model(array(
            'componentClass'=>'Vpc_Basic_Text_Component'
        ));
        $validTypes = array('image', 'link', 'download');
        $ccm = new Vpc_Basic_Text_ChildComponentsModel();
        $existingEntries = array();
        foreach ($ccm->fetchAll() as $row) {
            $existingEntries[] = $row->component_id.'-'.substr($row->component, 0, 1).$row->nr;
        }
        $validEntries = array();
        foreach ($t->fetchAll() as $row) {
            foreach ($row->getContentParts() as $part) {
                if (is_array($part) && in_array($part['type'], $validTypes)) {
                    $id = $row->component_id.'-'.substr($part['type'], 0, 1).$part['nr'];
                    $validEntries[] = $id;
                    if (in_array($id, $existingEntries)) {
                        $existingCount++;
                    } else {
                        $addedCount++;
                        $r = $ccm->createRow();
                        $r->component_id = $row->component_id;
                        $r->component = $part['type'];
                        $r->nr = $part['nr'];
                        $r->saved = 1;
                        $r->save();
                    }
                }
            }
        }
        foreach ($ccm->fetchAll() as $row) {
            $id = $row->component_id.'-'.substr($row->component, 0, 1).$row->nr;
            if (!in_array($id, $validEntries)) {
                $deletedCount++;
                $row->delete();
            }
        }
        echo "Converting text components:\n";
        echo "existing: $existingCount\n";
        echo "added: $addedCount\n";
        echo "deleted: $deletedCount\n";
        echo 'done in '.(microtime(true)-$start)."sec\n";
    }

    /**
     * Ersetzt vollwertige ids in DbIds
     *
     * zB: $search = '27-35_' und $replace = 'news_'
     */
    protected function _updateDbIds($search, $replace)
    {
        echo "converting all ids in all tables: (SEARCH) ".preg_replace('/\s+/', ' ', $search)." (REPLACE) ".preg_replace('/\s+/', ' ', $replace)."\n";
        $db = Zend_Registry::get('db');
        foreach ($db->query("SHOW TABLES")->fetchAll() as $table) {
            $table = array_values($table);
            $table = $table[0];
            $hasComponentId = false;
            foreach ($db->query("SHOW FIELDS FROM $table")->fetchAll() as $field) {
                if ($field['Field'] == 'component_id') $hasComponentId = true;
            }
            if ($hasComponentId) {
                $db->query("UPDATE $table SET component_id =
                        REPLACE(component_id, '$search', '$replace')
                        WHERE component_id LIKE '".str_replace('_', '\_', $search)."%'");
            }
        }
    }

    /**
     * Ersetzt komplexe component_ids
     */
    protected function _updateComplexDbIds($set, $where)
    {
        echo "converting all tables: (SET) ".preg_replace('/\s+/', ' ', $set)." (WHERE) ".preg_replace('/\s+/', ' ', $where)."\n";
        $db = Zend_Registry::get('db');
        foreach ($db->query("SHOW TABLES")->fetchAll() as $table) {
            $table = array_values($table);
            $table = $table[0];
            $hasComponentId = false;
            foreach ($db->query("SHOW FIELDS FROM $table")->fetchAll() as $field) {
                if ($field['Field'] == 'component_id') $hasComponentId = true;
            }
            if ($hasComponentId) {
                $db->query("UPDATE $table SET $set
                        WHERE $where");
            }
        }
    }

    /**
     * Gibt alle vorhandenen tables in einem array zurück
     */
    protected function _getTables()
    {
        $db = Zend_Registry::get('db');
        $tables = array();
        foreach ($db->query("SHOW TABLES")->fetchAll() as $table) {
            $table = array_values($table);
            $tables[] = $table[0];
        }
        return $tables;
    }

    /**
     * Gibt alle felder in einem array zurück
     */
    protected function _getColumns($table)
    {
        $db = Zend_Registry::get('db');
        $columns = array();
        foreach ($db->query("DESCRIBE $table")->fetchAll() as $field) {
            $columns[] = $field['Field'];
        }
        return $columns;
    }
}
