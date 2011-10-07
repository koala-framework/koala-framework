<?php
class Vpc_Root_Category_Trl_Update_33918 extends Vps_Update
{
    public function update()
    {
        
        $db = Zend_Registry::get('db');
        $pages = array();
        foreach ($db->query("SELECT id, parent_id FROM vps_pages")->fetchAll() as $p) {
            $pages[$p['id']] = $p['parent_id'];
        }

        $tables = array();
        foreach ($db->query("SHOW TABLES")->fetchAll() as $table) {
            $table = array_values($table);
            $table = $table[0];
            $hasComponentId = false;
            $primary = false;
            foreach ($db->query("SHOW FIELDS FROM $table")->fetchAll() as $field) {
                if ($field['Key'] == 'PRI') $primary = $field['Field'];
                if ($field['Field'] == 'component_id') $hasComponentId = true;
            }
            if (!$primary) {
                throw new Vps_Exception('primary key not found');
            }
            if ($hasComponentId) {
                $tables[] = array(
                    'table' => $table,
                    'field' => 'component_id',
                    'primary' => $primary
                );
            }
            if ($table == 'vpc_basic_link_intern') {
                $tables[] = array(
                    'table' => $table,
                    'field' => 'target',
                    'primary' => $primary
                );
            }
        }

        foreach ($tables as $tbl) {
            $table = $tbl['table'];
            $primary = $tbl['primary'];
            $field = $tbl['field'];
            $sql = "SELECT $primary AS id, $field FROM $table
                WHERE NOT $field LIKE 'root-master-%'
                AND NOT $field REGEXP '^\d*$'
                AND $field LIKE 'root-%'";
            foreach ($db->query($sql)->fetchAll() as $row) {
                $cid = $row[$field];
                if (preg_match('#^(root-[^-_]+-[^-_]+)_([\d_]*_\d+)(.*)$#', $cid, $m)) {
                    $parts = explode('_', $m[2]);
                    foreach ($parts as $k=>$i) {
                        if (!isset($pages[$i])) break;
                        $cid = $m[1].'_'.implode('_', array_slice(explode('_', $m[2].$m[3]), $k));
                    }
                    $sql = "DELETE FROM $table WHERE $field='$cid'";
                    echo "$sql\n";
                    $db->query($sql);
                    $sql = "UPDATE $table SET $field='$cid' WHERE $primary='".$row['id']."'";
                    echo "$sql\n";
                    $db->query($sql);
                }
            }
        }

        foreach ($db->query("SELECT component_id, content FROM vpc_basic_text")->fetchAll() as $row) {
            $changed = false;
            preg_match_all('#href="(root-[^-_]+-[^-_]+)_([\d_]*_\d+)#', $row['content'], $m);
            foreach (array_keys($m[0]) as $k) {
                if (preg_match('#^root-master-#', $m[1][$k])) continue;
                $new = false;
                foreach (explode('_', $m[2][$k]) as $l=>$i) {
                    if (!isset($pages[$i])) break;
                    $new = 'href="'.$m[1][$k].'_'.implode('_', array_slice(explode('_', $m[2][$k]), $l));
                }
                if ($new) {
                    $changed = true;
                    $row['content'] = str_replace($m[0][$k], $new, $row['content']);
                }
            }
            if ($changed) {
                $sql = "UPDATE vpc_basic_text SET content=".$db->quote($row['content'])." WHERE component_id='$row[component_id]'";
                echo "$sql\n";
                $db->query($sql);
            }
        }
    }
}
