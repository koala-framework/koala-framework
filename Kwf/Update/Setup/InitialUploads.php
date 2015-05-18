<?php
class Kwf_Update_Setup_InitialUploads extends Kwf_Update
{
    protected $_uploadsPath;
    public function __construct($uploadsPath)
    {
        $this->_uploadsPath = $uploadsPath;
        parent::__construct(null, null);
    }

    public function update()
    {
        $path = $this->_uploadsPath; //initial setup for web
        if (file_exists($path)) {
            $m = Kwf_Model_Abstract::getInstance('Kwf_Uploads_Model');
            foreach ($m->getRows() as $row) {
                if (file_exists($path.'/'.$row->id)) {
                    $row->copyFile($path.'/'.$row->id, $row->filename, $row->extension, $row->mime_type);
                }
            }
        }
    }
}
