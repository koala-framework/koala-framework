<?p
class Vpc_Basic_Image_Enlarge_Admin extends Vpc_Basic_Image_Adm

    public function setup
   
        $fields['filename'] = 'varchar(255) NOT NULL
        $fields['width'] = 'int(11) NOT NULL
        $fields['height'] = 'int(11) NOT NULL
        $fields['scale'] = 'varchar(255) NOT NULL
        $fields['enlarge'] = 'tinyint(4) DEFAULT NULL
        $fields['vps_upload_id'] = 'int(11) DEFAULT NULL
        $this->createFormTable('vpc_basic_image_enlarge', $fields
   

