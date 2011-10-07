<?php
class Vpc_Trl_DownloadTag_DownloadTag_UploadsModel extends Vps_Test_Uploads_Model
{
    public function __construct($config = array())
    {
        parent::__construct($config);

        $this->createRow()->copyFile(VPS_PATH.'/images/information.png', 'foo', 'png', 'image/png');
        $this->createRow()->copyFile(VPS_PATH.'/images/vividplanet.gif', 'vivid', 'gif', 'image/gif');
    }
}
