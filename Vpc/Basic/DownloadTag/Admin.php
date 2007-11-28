<?ph
class Vpc_Basic_DownloadTag_Admin extends Vpc_Basic_Image_Admi

    public function setup(
    
        $fields['filename'] = 'varchar(255) NOT NULL'
        $fields['vps_upload_id'] = 'int'
        $this->createFormTable('vpc_basic_downloadtag', $fields)
    

