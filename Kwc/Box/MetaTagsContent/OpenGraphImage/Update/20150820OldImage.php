<?php
class Kwc_Box_MetaTagsContent_OpenGraphImage_Update_20150820OldImage extends Kwf_Update
{
    public function update()
    {
        $db = Kwf_Registry::get('db');
        $rows = $db->query("SELECT component_id FROM kwc_basic_image WHERE component_id LIKE '%-openGraph-image'")->fetchAll();
        foreach ($rows as $row) {
            $id = substr($row['component_id'], 0, -16); //remove -openGraph-image
            if (is_numeric($id)) {
                //ignore non-pages
                $id .= '-metaTags-ogImage'; //-metaTags could be named differently, but in most (all!) cases that name is used
                $db->query("UPDATE kwc_basic_image SET component_id=?", array($id));
            }
        }
    }
}
