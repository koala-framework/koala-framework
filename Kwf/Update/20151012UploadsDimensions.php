<?php
class Kwf_Update_20151012UploadsDimensions extends Kwf_Update
{
    private $_countUploads;

    public function getProgressSteps()
    {
        $ret = count(Kwf_Model_Abstract::findAllInstances());
        if ($this->countUploads() < 5000) {
            $ret += $this->countUploads();
        }
        return $ret;
    }

    public function countUploads()
    {
        if (is_null($this->_countUploads)) {
            if (in_array('kwf_uploads', Kwf_Registry::get('db')->listTables())) {
                $this->_countUploads = Kwf_Registry::get('db')->query('SELECT COUNT(*) FROM kwf_uploads')->fetchColumn();
            } else {
                $this->_countUploads = 0;
            }
        }
        return $this->_countUploads;
    }

    public function update()
    {
        $db = Kwf_Registry::get('db');

        $db->query("ALTER TABLE  `kwf_uploads` ADD  `is_image` TINYINT NOT NULL;");
        $db->query("UPDATE `kwf_uploads` SET `is_image`=-1;"); //-1 means unknown
        $db->query("ALTER TABLE  `kwf_uploads` ADD  `image_width` INT NULL;");
        $db->query("ALTER TABLE  `kwf_uploads` ADD  `image_height` INT NULL;");
        $db->query("ALTER TABLE  `kwf_uploads` ADD  `image_rotation` INT NULL;");

        if ($this->countUploads() < 5000) {
            $this->calculateDimensions();
        } else {
            echo "More than 5000 Uploads. Please execute renaming manually:\n\"php bootstrap.php update-uploads calculate-dimensions\"\n\n";
        }
    }

    public function calculateDimensions()
    {
        $db = Kwf_Registry::get('db');
        $s = new Kwf_Model_Select();
        $it = new Kwf_Model_Iterator_Packages(
            new Kwf_Model_Iterator_Rows(Kwf_Model_Abstract::getInstance('Kwf_Uploads_Model'), $s)
        );
        foreach ($it as $row) {
            $this->_progressBar->next(1, 'calculating dimension '.$row->id);
            if (file_exists($row->getFileSource())) {
                $size = getimagesize($row->getFileSource());
                if ($size) {
                    $width = $size[0];
                    $height = $size[1];
                    $rotation = Kwf_Media_Image::getExifRotation($row->getFileSource());
                    $db->query("UPDATE `kwf_uploads` SET  `is_image`=1, `image_width` =  '{$width}', `image_height` =  '{$height}', `image_rotation` =  '{$rotation}' WHERE  `id` = '{$row->id}';");
                }
            }
        }
    }
}
