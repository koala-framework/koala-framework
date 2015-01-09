<?php
class Kwf_Update_39000 extends Kwf_Update
{
    public function update()
    {
        $db = Kwf_Registry::get('db');
        $db->query("ALTER TABLE  `kwf_uploads` ADD  `md5_hash` VARCHAR( 32 ) NOT NULL");
        $db->query("ALTER TABLE  `kwf_uploads` ADD INDEX  `md5_hash` (  `md5_hash` )");
        $s = new Kwf_Model_Select();
        $s->whereEquals('md5_hash', '');
        $it = new Kwf_Model_Iterator_Packages(
            new Kwf_Model_Iterator_Rows(Kwf_Model_Abstract::getInstance('Kwf_Uploads_Model'), $s)
        );
        $it = new Kwf_Iterator_ConsoleProgressBar($it);
        foreach ($it as $row) {
            if (file_exists($row->getFileSource())) {
                $row->md5_hash = md5_file($row->getFileSource());
                $row->save();
            }
        }

        if (in_array('kwc_basic_image', $db->listTables())) {
            $m = Kwf_Model_Abstract::getInstance('Kwc_Abstract_Image_Model');
            $s->whereEquals('filename', '');
            $it = new Kwf_Model_Iterator_Packages(
                new Kwf_Model_Iterator_Rows(Kwf_Model_Abstract::getInstance('Kwf_Uploads_Model'), $s)
            );
            $it = new Kwf_Iterator_ConsoleProgressBar($it);
            foreach ($it as $row) {
                $pr = $row->getParentRow('Image');
                if ($pr) {
                    $row->filename = $pr->filename;
                    $row->save();
                }
            }
        }
    }
}
