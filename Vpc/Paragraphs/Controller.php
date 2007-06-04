<?php
class Vpc_Paragraphs_Controller extends Vpc_Controller
{
    public function indexAction()
    {
        $iniComponents = new Zend_Config_Ini('../application/config.ini', 'components');

        $cfg = array();
        $cfg['components'] = $iniComponents->components->toArray();

        $this->_render(array(), '', $cfg);
    }
       
    public function ajaxDataAction()
    {
        $id = $this->getRequest()->getParam('id');
        $table = Zend_Registry::get('dao')->getTable('Vps_Dao_Paragraphs');

        $iniComponents = new Zend_Config_Ini('../application/config.ini', 'components');
        $components = $iniComponents->components->toArray();
        
        $paragraphs = $table->fetchParagraphsData($id);
        foreach ($paragraphs as $key => $paragraph) {
            if (isset($components[$paragraph['component']])) {
                $paragraphs[$key]['component'] = $components[$paragraph['component']];
            }
        }
        $this->getResponse()->appendJson('rows', $paragraphs);
    }
       
    public function ajaxCreateAction()
    {
        $componentClass = $this->getRequest()->getParam('componentClass');
        $lastSiblingId = $this->getRequest()->getParam('componentId');
        $id = $this->getRequest()->getParam('id');

        $table = Zend_Registry::get('dao')->getTable('Vps_Dao_Paragraphs');

        $componentId = $table->createParagraph($id, $componentClass, $lastSiblingId);
        $this->getResponse()->appendJson('componentId', $componentId);
    }

    public function ajaxDeleteAction()
    {
        $table = Zend_Registry::get('dao')->getTable('Vps_Dao_Paragraphs');
        $componentIds = explode(',', $this->getRequest()->getParam('componentIds'));
        foreach ($componentIds as $componentId) {
            $table->deleteParagraph($componentId);
        }
        $this->getResponse()->appendJson('success', true);
    }

    public function ajaxMoveAction()
    {
        $table = Zend_Registry::get('dao')->getTable('Vps_Dao_Paragraphs');
        $id = $this->getRequest()->getParam('id');
        $componentIds = explode(',', $this->getRequest()->getParam('componentIds'));
        foreach ($componentIds as $componentId) {
            $table->moveParagraph($id, $componentId, $this->getRequest()->getParam('direction'));
        }
        $this->getResponse()->appendJson('success', true);
    }

    public function ajaxStatusAction()
    {
        $status = $this->getRequest()->getParam('status');
        if ($status != 'online' && $status != 'offline') {
            throw new Vpc_Exception('Status must either be online or offline.');
        }
        
        $componentIds = explode(',', $this->getRequest()->getParam('componentIds'));
        $table = Zend_Registry::get('dao')->getTable('Vps_Dao_Components');
        foreach ($componentIds as $componentId) {
            $table->setStatus($componentId, $status == 'online');
        }
        $this->getResponse()->appendJson('success', true);
    }

}
