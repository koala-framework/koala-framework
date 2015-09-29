<?php
class Kwc_Box_MetaTagsContent_Trl_Update_20150929ConvertMasterAsChild extends Kwf_Update
{
    public function update()
    {
        $db = Kwf_Registry::get('db');
        $rows = $db->query("SELECT component_id FROM kwc_data WHERE component_id LIKE 'root-%metaTags-child'")->fetchAll();
        foreach ($rows as $row) {
            $componentId = $row['component_id'];
            $newComponentId = str_replace('-metaTags-child', '-metaTags', $componentId);
            $db->query("UPDATE kwc_data SET component_id=? WHERE component_id=?", array($newComponentId, $componentId));
        }
    }
}
