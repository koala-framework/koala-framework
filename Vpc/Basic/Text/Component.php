<?ph
class Vpc_Basic_Text_Component extends Vpc_Basic_Html_Componen

    protected $_componentParts = array()

    public static function getSettings(
    
        return array_merge(parent::getSettings(), array
            'tablename'         => 'Vpc_Basic_Text_Model'
            'componentName'     => 'Standard.Text'
            'fieldLabel'        => 'Rich Text Editor'
            'width'             => 550
            'height'            => 400
            'enableAlignments'  => true
            'enableColors'      => false
            'enableFont'        => false
            'enableFontSize'    => false
            'enableFormat'      => true
            'enableLists'       => true
            'enableSourceEdit'  => true
            'childComponentClasses' => array
                'image'         => 'Vpc_Basic_Text_Image_Component'
                'link'          => 'Vpc_Basic_LinkTag_Component'
                'download'      => 'Vpc_Basic_DownloadTag_Component
            )
            'default'           => array
                'content'       => '<p>'.Vpc_Abstract::LOREM_IPSUM.'</p>
            
        ))
    

    protected function _init(
    
        parent::_init()

        foreach ($this->_row->getContentParts() as $part) 
            if (is_array($part)) 
                if ($part['type'] == 'image') 
                    $class = $this->_getClassFromSetting('image', 'Vpc_Basic_Image_Component')
                    $part['nr'] = 'i'.$part['nr']
                } else if ($part['type'] == 'link') 
                    $class = $this->_getClassFromSetting('link', 'Vpc_Basic_LinkTag_Component')
                    $part['nr'] = 'l'.$part['nr']
                } else if ($part['type'] == 'download') 
                    $class = $this->_getClassFromSetting('download', 'Vpc_Basic_DownloadTag_Component')
                    $part['nr'] = 'd'.$part['nr']
                
                $component = $this->createComponent($class, $part['nr'])
                $this->_componentParts[] = $component
            } else 
                $this->_componentParts[] = $part
            
        
    

    public function getChildComponents(
    
        $ret = array()
        foreach ($this->_componentParts as $part) 
            if ($part instanceof Vpc_Abstract) 
                $ret[] = $part
            
        
        return $ret
    

    public function getTemplateVars(
    
        $ret = parent::getTemplateVars()
        foreach ($this->_componentParts as $part) 
            if ($part instanceof Vpc_Abstract) 
                $ret['contentParts'][] = $part->getTemplateVars()
            } else 
                $ret['contentParts'][] = $part
            
        
        return $ret
    

