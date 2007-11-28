<?ph
class Vps_Auto_Field_ImageViewer extends Vps_Auto_Field_Abstrac

    public function __construct($field_name = null, $field_label = null
    
        parent::__construct($field_name, $field_label)
        $this->setXtype('imageviewer')
    

    public function load($row
    
        $data = array()
        $data['imageUrl'] = $row->getImageUrl('default')
        $data['previewUrl'] = $row->getImageUrl('thumb')
        return array($this->getFieldName() => $data)
    

