<?php
class Vps_Controller_Action_Component_ComponentsController extends Vps_Controller_Action_Auto_Tree
{
    protected $_primaryKey = 'component';
    protected $_textField = 'component';
    protected $_buttons = array(
        'reload'    => true
    );
    protected $_rootVisible = false;
    protected $_icons = array (
        'root'      => 'asterisk_yellow',
        'page'      => 'page',
        'component' => 'page_white',
        'box'       => 'page_white_database',
        'default'   => 'page_error'
    );
    
    public function init()
    {
        $class = $this->_getParam('class');
        $this->_model = new Vps_Component_Generator_Model(array('default' => $class));
        parent::init();
    }
    
    public function jsonMetaAction()
    {
        parent::jsonMetaAction();
        $this->view->baseParams = array('class' => $this->_getParam('class'));
    }
    
    protected function _formatNode($row)
    {
        $data = parent::_formatNode($row);
        if ($row->class == 'root') {
            $icon = 'root';
        } else if (is_instance_of($row->class, 'Vps_Component_Generator_Box_Interface')) {
            $icon = 'box';
        } else if (is_instance_of($row->class, 'Vps_Component_Generator_Page_Interface')) {
            $icon = 'page';
        } else {
            $icon = 'component';            
        }
        $data['expanded'] = $row->class == 'root';
        $data['bIcon'] = $this->_icons[$icon]->__toString();
        $data['text'] .= ': ' . $row->name;
        return $data;
    }
}
