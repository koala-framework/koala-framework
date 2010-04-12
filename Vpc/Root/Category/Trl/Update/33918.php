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
                $sql = "SELECT $primary AS id, component_id FROM $table
                    WHERE NOT component_id LIKE 'root-master-%'
                    AND NOT component_id REGEXP '^\d*$'
                    AND component_id LIKE 'root-%'";
                foreach ($db->query($sql)->fetchAll() as $row) {
                    $cid = $row['component_id'];
                    if (preg_match('#^(root-[^-_]+-[^-_]+)_([\d_]*_\d+)#', $cid, $m)) {
                        foreach (explode('_', $m[2]) as $i) {
                            if (!isset($pages[$i])) break;
                            $cid = $m[1].'_'.$i;
                        }
                        $db->query("DELETE FROM $table WHERE $primary='$row[id]'");
                        $sql = "UPDATE $table SET component_id='$cid' WHERE $primary='".$row['id']."'";
                        //echo "$sql\n";
                        $db->query($sql);
                    }
                }
            }
        }
        foreach ($db->query("SELECT component_id, content FROM vpc_basic_text")->fetchAll() as $row) {
            $changed = false;
            preg_match_all('#href="(root-[^-]+-[^-]+)_([\d_]*_\d+)#', $row['content'], $m);
            foreach (array_keys($m[0]) as $k) {
                if (preg_match('^root-master-', $m[1][$k])) continue;
                $new = false;
                foreach (explode('_', $m[2][$k]) as $i) {
                    if (!isset($pages[$i])) break;
                    $new = 'href="'.$m[1][$k].'_'.$i;
                }
                if ($new) {
                    $changed = true;
                    $row['content'] = str_replace($m[0][$k], $new, $row['content']);
                }
            }
            if ($changed) {
                $sql = "UPDATE vpc_basic_text SET content=".$db->quote($row['content'])." WHERE component_id='$row[component_id]'";
                //echo "$sql\n";
                $db->query($sql);
            }
        }
    }
}
