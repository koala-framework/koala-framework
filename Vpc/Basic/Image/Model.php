<?ph
class Vpc_Basic_Image_Model extends Vpc_Tabl

    protected $_name = 'vpc_basic_image'
    protected $_rowClass = 'Vpc_Basic_Image_Row'
    protected $_referenceMap    = array
        'File' => array
            'columns'           => array('vps_upload_id')
            'refTableClass'     => 'Vps_Dao_File'
            'refColumns'        => array('id'
        
    )

