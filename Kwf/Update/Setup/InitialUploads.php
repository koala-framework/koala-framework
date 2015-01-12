<?php
class Kwf_Update_Setup_InitialUploads extends Kwf_Update
{
    public function __construct()
    {
        parent::__construct(null, null);
    }

    public function update()
    {
        $file = 'setup/uploads'; //initial setup for web
        if (file_exists($file)) {
            $m = Kwf_Model_Abstract::getInstance('Kwf_Uploads_Model');
            foreach ($m->getRows() as $row) {
                if (file_exists('setup/uploads/'.$row->id)) {
                    $dir = dirname($row->getFileSource());
                    if (!file_exists($dir)) mkdir($dir);
                    copy('setup/uploads/'.$row->id, $row->getFileSource());
                }
            }
        }
    }
}
