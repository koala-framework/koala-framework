<?ph
class Vpc_Composite_Images_Component extends Vpc_Abstrac

    public $images = array()
   
    public static function getSettings(
    
        return array_merge(parent::getSettings(), array
            'componentName' => 'Standard.Images'
            'tablename'     => 'Vpc_Composite_Images_Model'
            'childComponentClasses' => array
                'image'         => 'Vpc_Basic_Image_Enlarge_Component
            
        ))
    
   
    protected function _init(
    
        $where = array
            'page_id = ?' => $this->getDbId()
            'component_key = ?' => $this->getComponentKey(
        )
        if (!$this->showInvisible()) 
            $where['visible = ?'] = 1
        
        $imageClass = $this->_getClassFromSetting('image', 'Vpc_Basic_Image_Component')
        foreach ($this->getTable()->fetchAll($where) as $row) 
            $this->images[$row->id] = $this->createComponent($imageClass, $row->id)
        
    

    public function getTemplateVars(
    
        $return = parent::getTemplateVars()
        $return['images'] = array()
        foreach ($this->images as $c) 
            $return['images'][] = $c->getTemplateVars()
        
        return $return
    

    public function getChildComponents(
    
        return $this->images
    

