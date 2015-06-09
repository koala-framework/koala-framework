<?php
class Kwc_Columns_Update_20150309Legacy00001 extends Kwf_Update
{
    //required in postUpdate as we use getComponentsByClass which would possibly not work in update()
    public function postUpdate()
    {
        $search = array();
        $replace = array();
        $dbPattern = array();
        $model = null;
        $j = 0;

        $components = Kwf_Component_Data_Root::getInstance()
            ->getComponentsByClass('Kwc_Columns_Component', array('ignoreVisible' => true));
        if (count($components) > 50) {
            echo "Kwc_Columns_Update_1: updating ".count($components)." components, this might take some time\n";
        }
        foreach ($components as $c) {
            if (!$model) $model = $c->getComponent()->getChildModel();
            $totalColumns = (int)substr($c->getComponent()->getRow()->type, 0, 1);

            $select = new Kwf_Model_Select();
            $select->whereEquals('component_id', $c->dbId);
            if ($model->countRows($select)) continue;

            for ($i = 1; $i <= $totalColumns; $i++) {
                $id = $model->createRow(array(
                    'component_id' => $c->dbId,
                    'pos' => $i,
                    'visible' => true
                ))->save();

                $s = $c->dbId . '-' . $i;
                $search[$j] = $s;
                $replace[$j] = $c->dbId . '-' . $id;

                $dbPattern[$j][] = $s . '-%';
                $dbPattern[$j][] = $s . '\_%';
                $dbPattern[$j][] = $s;

                $j++;
            }
        }

        if (empty($search)) return;

        $db = Zend_Registry::get('db');
        foreach ($db->listTables() as $table) {
            if ($table == 'cache_component') continue;
            if ($table == 'cache_component_includes') continue;
            if ($table == 'cache_component_url') continue;
            if ($table == 'cache_users') continue;
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
                $where = "WHERE ";
                foreach ($search as $key => $value) {
                    $where .= "$column LIKE '" . implode("' OR $column LIKE '", $dbPattern[$key]) . "' OR ";
                }
                $where = substr($where, 0, strlen($where)-4);
                $sql = "SELECT $column FROM $table $where";
                $ids = $db->fetchCol($sql);
                if (empty($ids)) continue;

                $lastUpdatedId = false;
                foreach ($ids as $id) {
                    if ($lastUpdatedId == $id) continue;
                    $k = 0;
                    foreach ($search as $key => $value) {
                        if (strpos($id, $value) !== false) {
                            $k = $key;
                            break;
                        }
                    }
                    $sql = "UPDATE $table SET $column = " . $db->quote(str_replace($search[$k], $replace[$k], $id)) . "
                        WHERE $column = '$id'";
                    $db->query($sql);
                    $lastUpdatedId = $id;
                }
            }
        }

        $where = "WHERE ";
        foreach ($search as $key => $value) {
            $where .= "content LIKE '%href=\"" . implode("\"%' OR content LIKE '%href=\"", $dbPattern[$key]) . "\"%' OR content LIKE '%href=\n  \"" . implode("\"%' OR content LIKE '%href=\n  \"", $dbPattern[$key]) . "\"%' OR ";
        }
        $where = substr($where, 0, strlen($where)-4);

        $sql = "SELECT component_id, content FROM kwc_basic_text $where";
        $rows = $db->query($sql)->fetchAll();
        foreach ($rows as $row) {
            $k = 0;
            foreach ($search as $key => $value) {
                if (strpos($row['content'], $value) !== false) {
                    $row['content'] = preg_replace('#(href=\s*")([^"]*/)?([^"]*)'.preg_quote($search[$key]).'([^"]*)"#',
                                                   '\1\2\3'.$replace[$key].'\4"',
                                                   $row['content']);
                }
            }
            $sql = "UPDATE kwc_basic_text SET content = " . $db->quote($row['content']) . "
                        WHERE component_id = '".$row['component_id']."'";
            $db->query($sql);
        }
    }
}
