<?php
class Kwc_Abstract_Cards_Update_20150309Legacy00002 extends Kwf_Update
{
    public function update()
    {
        if (Kwf_Registry::get('db')->fetchAll('SHOW TABLES LIKE "kwc_basic_linktag"')) {
            Kwf_Registry::get('db')->query("REPLACE INTO kwc_basic_cards (SELECT component_id, component FROM kwc_basic_linktag)");
            Kwf_Registry::get('db')->query('DROP TABLE kwc_basic_linktag');
        }

        $ids = array();
        $search = array();
        $replace = array();
        $componentIds = array();
        foreach (Kwf_Registry::get('db')->fetchAll('SELECT component_id, component FROM kwc_basic_cards') as $row) {
            $componentIds[$row['component_id']] = true;
            $ids[] = $row['component_id'];
            $search[$row['component_id']] = '#^'.preg_quote($row['component_id'] . '-link').'([\\-_].+)?$#';
            $replace[$row['component_id']] = $row['component_id'] . '-child';
        }

        $db = Zend_Registry::get('db');
        foreach ($db->query("SHOW TABLES")->fetchAll() as $table) {
            $table = array_values($table);
            $table = $table[0];
            $hasComponentId = false;
            $column = 'component_id';
            foreach ($db->query("SHOW FIELDS FROM $table")->fetchAll() as $field) {
                if ($table == 'kwf_pages') {
                    $column = 'parent_id';
                    $hasComponentId = true;
                } else if ($field['Field'] == 'component_id') {
                    $hasComponentId = true;
                }
            }
            if ($hasComponentId) {
                echo "\n".$table;
                $sql = "SELECT $column FROM $table WHERE $column LIKE '%-link'";
                $notInList = array();
                foreach ($db->query($sql) as $row) {
                    $componentId = substr($row[$column], 0, -5);
                    if (!isset($componentIds[$componentId])) {
                        $notInList[] = $row[$column];
                    }
                }
                $sql = "UPDATE $table
                    SET $column = CONCAT(SUBSTR($column, 1, (length($column) -4)),'child')
                    WHERE $column LIKE '%-link' AND $column NOT IN ('" . implode("', '", $notInList) . "')";
                $db->query($sql);

                $sql = "SELECT $column FROM $table WHERE $column NOT LIKE '%-link' AND $column LIKE '%-link%'";
                foreach ($db->query($sql)->fetchAll() as $row) {
                    $componentId = $row[$column];
                    $componentId = substr($componentId, 0, strrpos($componentId, '-link') + 5);
                    if (isset($componentIds[$componentId])) {
                        $newId = preg_replace($search, $replace, $row[$column]);
                        $sql = "UPDATE $table SET $column=".$db->quote($newId)." WHERE $column=".$db->quote($row[$column]);
                        $db->query($sql);
                    }
                }
            }
        }

        echo "\n\nkwc_basic_text:\n";
        $componentIds = array_flip($db->fetchCol("select component_id from kwc_basic_text WHERE content LIKE '%-link%'"));
        foreach ($search as $k=>$s) {
            if (!isset($componentIds[$k])) continue;
            $r = $replace[$k];
            $db->query("UPDATE kwc_basic_text SET content =
                    REPLACE(content, 'href=\"$s-', 'href=\"$r-') WHERE content LIKE '$s'");
            $db->query("UPDATE kwc_basic_text SET content =
                    REPLACE(content, 'href=\n  \"$s-', 'href=\n  \"$r-') WHERE content LIKE '$s'");
        }
    }
}