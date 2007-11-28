<?ph
class Vpc_Basic_Text_Controller extends Vps_Controller_Action_Auto_Vpc_For

    public function _initFields(
    
        $field = new Vps_Auto_Field_HtmlEditor('content', 'Content')
        $field->setData(new Vps_Auto_Data_Vpc_ComponentIds('content'))

        $ignoreSettings = array('tablename', 'componentName', 'childComponentClasses', 'default')
        foreach (call_user_func(array($this->class, 'getSettings')) as $key => $val) 
            if (!in_array($key, $ignoreSettings)) 
                $method = 'set' . ucfirst($key)
                $field->$method($val)
            
        
        $classes = Vpc_Abstract::getSetting($this->class, 'childComponentClasses')
        $field->setLinkComponentConfig(Vpc_Admin::getConfig($classes['link']))
        $field->setImageComponentConfig(Vpc_Admin::getConfig($classes['image']))
        $field->setDownloadComponentConfig(Vpc_Admin::getConfig($classes['download']))

        $field->setControllerUrl(Vpc_Admin::getInstance($this->class)->getControllerUrl())

        $this->_form->add($field)
    

    protected function _beforeSave(Zend_Db_Table_Row_Abstract $row
    
        $row->content_edit = ''
        parent::_beforeSave($row)
    

    public function jsonTidyHtmlAction(
    
        $html = $this->_getParam('html')

        $html = preg_replace('#(<o:p.*>)#', '', $html)

        $stripProps = array('style', 'class', 'width', 'valign')
        foreach ($stripProps as $i) 
            $html = preg_replace('#(<.+)'.preg_quote($i).'="[^"]*"(.*>)#', '\\1\\2', $html)
        
        $row = $this->_form->getRow()

        $row->content_edit = $row->tidy($html)
        $row->save()
        $this->view->html = $row->content_edit
    

    public function jsonAddImageAction(
    
        $classes = Vpc_Abstract::getSetting($this->class, 'childComponentClasses')

        $row = $this->_form->getRow()
        $this->view->page_id = $row->page_id
        $this->view->component_key = $row->component_key.'-i'
                                        ($row->getMaxChildComponentNr('image')+1)
        $imageClass = Vpc_Abstract::getSetting($this->class, 'imageClass')
        $row->content_edit .= "<img src=\"/media/0/$classes[image]/{$this->view->page_id}{$this->view->component_key}/\" />"
        $row->save()
    
    public function jsonAddLinkAction(
    
        $row = $this->_form->getRow()
        $this->view->page_id = $row->page_id
        $this->view->component_key = $row->component_key.'-l'
                                        ($row->getMaxChildComponentNr('link')+1)
        $row->content_edit .= "<a href=\"{$this->view->page_id}{$this->view->component_key}\" />"
        $row->save()
    
    public function jsonAddDownloadAction(
    
        $row = $this->_form->getRow()
        $this->view->page_id = $row->page_id
        $this->view->component_key = $row->component_key.'-d'
                                        ($row->getMaxChildComponentNr('download')+1)
        $row->content_edit .= "<a href=\"{$this->view->page_id}{$this->view->component_key}\" />"
        $row->save()
    

