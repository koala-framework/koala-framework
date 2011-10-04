<?php
class Vpc_Basic_Flash_Update_35824 extends Vps_Update
{
    public function update()
    {
        $model = new Vps_Component_FieldModel();
        $db = Vps_Registry::get('db');
        foreach ($db->query("SELECT * FROM vpc_basic_flash")->fetchAll() as $row) {
            if ($row['flash_source_type'] != 'external_flash_url') {
                echo "\n\nACHTUNG FLASH mit upload konnte nicht konvertiert werden ($row[component_id])\n\n";
                continue;
            }
            $newRow = $model->getRow($row['component_id']);
            if (!$newRow) {
                $newRow = $model->createRow();
                $newRow->component_id = $row['component_id'];
            }
            $code  = "<object width=\"$row[width]\" height=\"$row[height]\">\n";
            $url = $row['external_flash_url'];
            $vars = array();
            foreach ($db->query("SELECT * FROM vpc_basic_flash_vars WHERE parent_id='$row[component_id]'") as $v) {
                $vars[] = urlencode($v['key']).'='.urlencode($v['value']);
            }
            if ($vars) $url .= "?".implode('&', $vars);
            $code .= "  <param name=\"movie\" value=\"".htmlspecialchars($url)."\">\n";
            $code .= "  <param name=\"allow_fullscreen\" value=\"$row[allow_fullscreen]\">\n";
            $code .= "  <param name=\"menu\" value=\"$row[menu]\">\n";
            $code .= "</object>\n";
            $newRow->code = $code;
            $newRow->save();
        }

        //und tschüss
        $db->query("DROP TABLE vpc_basic_flash");
        $db->query("DROP TABLE vpc_basic_flash_vars");
    }
}