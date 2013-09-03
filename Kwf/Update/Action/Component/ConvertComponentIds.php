<?php
class Kwf_Update_Action_Component_ConvertComponentIds extends Kwf_Update_Action_Abstract
{
    public $search;
    public $replace;

    public function checkSettings()
    {
        parent::checkSettings();
        if (!$this->search || is_null($this->replace)) {
            throw new Kwf_ClientException("Required parameters: search, replace");
        }
    }

    public function update()
    {
        $search = $this->search;
        $replace = $this->replace;
        $pattern = isset($this->pattern) ? $this->pattern : $search . '%';
        $overwrite = isset($this->overwrite) && $this->overwrite;

        $dbPattern = str_replace('_', '\_', $pattern);

        $db = Zend_Registry::get('db');

        foreach ($db->query("SHOW TABLES")->fetchAll() as $table) {
            $table = array_values($table);
            $table = $table[0];
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
        $db->query("UPDATE kwc_basic_text SET content =
                REPLACE(content, 'href=\"$search', 'href=\"$replace') WHERE content LIKE '%href=\"$dbPattern\"%'");
        $db->query("UPDATE kwc_basic_text SET content =
                REPLACE(content, 'href=\n  \"$search', 'href=\"$replace') WHERE content LIKE '%href=\n  \"$dbPattern\"%'");
        foreach ($db->query("SELECT component_id, content FROM kwc_basic_text WHERE content LIKE '%href=\"%$dbPattern\"%' OR content LIKE '%href=\n  \"%$dbPattern\"%'")->fetchAll() as $r) {
            $r['content'] = preg_replace('#(href=\s*")([^"]*/)?([^"]*)'.preg_quote($search).'([^"]*)"#', '\1\2\3'.$replace.'\4"', $r['content']);
            $db->update('kwc_basic_text', array('content' => $r['content']), 'component_id='.$db->quote($r['component_id']));
        }
        //TODO: Images

    }
}
