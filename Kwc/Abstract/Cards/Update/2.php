<?php
class Kwc_Abstract_Cards_Update_2 extends Kwf_Update
{
    public function update()
    {
        if (Kwf_Registry::get('db')->fetchAll('SHOW TABLES LIKE "kwc_basic_linktag"')) {
            foreach (Kwf_Registry::get('db')->fetchAll('SELECT component_id, component FROM kwc_basic_linktag') as $row) {
                $sql = "REPLACE INTO kwc_basic_cards SET component_id='{$row['component_id']}', component='{$row['component']}'";
                Kwf_Registry::get('db')->query($sql);
                $sql = "DELETE FROM kwc_basic_linktag WHERE component_id='{$row['component_id']}'";
                Kwf_Registry::get('db')->query($sql);
            }
            Kwf_Registry::get('db')->query('DROP TABLE kwc_basic_linktag');
        }
        $ids = array();
        $search = array();
        $replace = array();
        foreach (Kwf_Registry::get('db')->fetchAll('SELECT component_id, component FROM kwc_basic_cards') as $row) {
            $ids[] = $row['component_id'];
            $search[] = '#^'.preg_quote($row['component_id'] . '-link').'([\\-_].+)?$#';
            $replace[] = $row['component_id'] . '-child';

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
                echo "\n\n".$table.":\n";
                $sql = "SELECT $column FROM $table WHERE 0 ";
                foreach ($ids as $id) {
                    $sql .= " OR $column LIKE ".$db->quote(str_replace('_', '\_', $id.'-link').'%');
                }
                foreach ($db->query($sql)->fetchAll() AS $row) {
                    $newId = preg_replace($search, $replace, $row[$column]);
                    $sql = "UPDATE $table SET $column=".$db->quote($newId)." WHERE $column=".$db->quote($row[$column]);
                    echo $sql."\n";
                    $db->query($sql);
                }
            }
        }

        echo "\n\nkwc_basic_text:\n";
        foreach ($search as $k=>$s) {
            $r = $replace[$k];
            $db->query("UPDATE kwc_basic_text SET content =
                    REPLACE(content, 'href=\"$s-', 'href=\"$r-') WHERE content LIKE '$s'");
            $db->query("UPDATE kwc_basic_text SET content =
                    REPLACE(content, 'href=\n  \"$s-', 'href=\n  \"$r-') WHERE content LIKE '$s'");
        }
    }
}