<?ph
class Vps_Controller_Action_Component_Media extends Vps_Controller_Action_Medi

    protected function _createChecksum(
    
        return md5(Vps_Media_Password::PASSWORD 
                    $this->_getParam('componentId').$this->_getParam('type'))
    

    protected function _getCacheFilename(
    
        return $this->_getParam('componentId') . $this->_getParam('type')
    

    protected function _createCacheFile($source, $target, $type
    
        $id = $this->_getParam('componentId')
        $class = $this->_getParam('class')
        /
        if (!$class) 
            $pageCollection = Vps_PageCollection_TreeBase::getInstance()
            $class = get_class($pageCollection->findComponent($id))
        
        *
        $tablename = Vpc_Abstract::getSetting($class, 'tablename')
        $table = new $tablename(array('componentClass'=>$class))
        $row = $table->findRow($id)
        $row->createCacheFile($source, $target, $type)
    
