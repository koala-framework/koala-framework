<?php
class Vps_Update_Action_Component_ConvertComponentIds extends Vps_Update_Action_Abstract
{
    public $search;
    public $replace;

    public function checkSettings()
    {
        parent::checkSettings();
        if (!$this->search || !$this->replace) {
            throw new Vps_ClientException("Required parameters: search, replace");
        }
    }

    public function update()
    {
        $search = $this->search;
        $replace = $this->replace;
        $pattern = isset($this->pattern) ? $this->pattern : $search . '%';
        $overwrite = isset($this->overwrite) && $this->overwrite;

        $db = Zend_Registry::get('db');
        foreach ($db->query("SHOW TABLES")->fetchAll() as $table) {
            $table = array_values($table);
            $table = $table[0];
            $hasComponentId = false;
            $column = 'component_id';
            foreach ($db->query("SHOW FIELDS FROM $table")->fetchAll() as $field) {
                if ($table == 'vps_pages') {
                    $column = 'parent_id';
                    $hasComponentId = true;
                } else if ($field['Field'] == 'component_id') {
                    $hasComponentId = true;
                }
            }
            if ($hasComponentId) {
                $dbPattern = str_replace('_', '\_', $pattern);
                if ($overwrite) {
                    $sql = "(SELECT REPLACE($column, '$search', '$replace')
                            FROM $table WHERE $column LIKE '$dbPattern')";
                    $ids = $db->fetchCol($sql);
                    $sql = "DELETE FROM $table
                        WHERE $column IN ('" . implode("', '", $ids) . "')";
                    $db->query($sql);
                }
                $db->query("UPDATE $table SET $column =
                        REPLACE($column, '$search', '$replace')
                        WHERE $column LIKE '$dbPattern'");
            }
        }
        $db->query("UPDATE vpc_basic_text SET content =
                REPLACE(content, 'href=\"$search-', 'href=\"$replace-')");
        //TODO: Images

    }
}
