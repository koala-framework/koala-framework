<?ph
class Vpc_Basic_DownloadTag_Component extends Vpc_Abstrac

    public static function getSettings(
    
        return array_merge(parent::getSettings(), array
            'tablename' => 'Vpc_Basic_DownloadTag_Model'
            'componentName' => 'Standard.Download Tag'
            'default'   => array
            
        ))
    

    public function getTemplateVars(
    
        $row = $this->_row
        $fileRow = $this->_row->findParentRow('Vps_Dao_File')
        if ($fileRow) 
            $url = $fileRow->generateUrl
                get_class($this)
                $this->getId(),
                $row->filename != '' ? $row->filename : 'unnamed',
                Vps_Dao_Row_File::DOWNLOA
            )
            $filesize = $fileRow->getFileSize()
            $filename = $row->filename . '.' . $fileRow->extension
        } else 
            $url = ''
            $filesize = 0
            $filename = ''
        

        $return = parent::getTemplateVars()
        $return['filesize'] = $filesize
        $return['url'] = $url
        $return['filename'] = $filename
        return $return
    

