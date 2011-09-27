<?php
class Vpc_Paragraphs_Controller extends Vps_Controller_Action_Auto_Vpc_Grid
{
    protected $_permissions = array(
        'save',
        'delete',
    );
    protected $_position = 'pos';

    protected function _initColumns()
    {
        $this->_columns->add(new Vps_Grid_Column('component_class'))
            ->setData(new Vps_Data_Vpc_ComponentClass($this->_getParam('class')));
        $this->_columns->add(new Vps_Grid_Column('component_name'))
            ->setData(new Vps_Data_Vpc_ComponentName($this->_getParam('class')));
        $this->_columns->add(new Vps_Grid_Column('component_icon'))
            ->setData(new Vps_Data_Vpc_ComponentIcon($this->_getParam('class')));

        $this->_columns->add(new Vps_Grid_Column('preview'))
            ->setData(new Vps_Data_Vpc_Frontend($this->_getParam('class')))
            ->setRenderer('component');
        $this->_columns->add(new Vps_Grid_Column_Visible());
        $this->_columns->add(new Vps_Grid_Column('edit_components'))
            ->setData(new Vpc_Paragraphs_EditComponentsData($this->_getParam('class')));
    }

    public function jsonDataAction()
    {
        parent::jsonDataAction();
        $this->view->componentConfigs = $this->_columns['edit_components']
                                ->getData()->getComponentConfigs();
        $c = Vps_Component_Data_Root::getInstance()
            ->getComponentByDbId($this->_getParam('componentId'), array('limit'=>1, 'ignoreVisible'=>true));
        $this->view->contentWidth = $c->getComponent()->getContentWidth();
    }

    public function preDispatch()
    {
        parent::preDispatch();
        $this->_components = array();
        foreach (Vpc_Abstract::getChildComponentClasses($this->_getParam('class'), 'paragraphs') as $c) {
            if (Vpc_Abstract::hasSetting($c, 'componentName')) {
                $name = Vpc_Abstract::getSetting($c, 'componentName');
                if ($name) $this->_components[$name] = $c;
            }
        }
    }

    protected function _getSelect()
    {
        $ret = parent::_getSelect();
        if ($this->_getParam('filter_visible')) {
            $ret->whereEquals('visible', $this->_getParam('filter_visible'));
        }
        return $ret;
    }

    public function jsonAddParagraphAction()
    {
        $class = $this->_getParam('component');
        if (array_search($class, $this->_components)) {
            $admin = Vpc_Admin::getInstance($class);
            if ($admin) $admin->setup();
            $row = $this->_model->createRow();
            $this->_preforeAddParagraph($row);
            $generators = Vpc_Abstract::getSetting($this->_getParam('class'), 'generators');
            $classes =$generators['paragraphs']['component'];
            $row->component = array_search($class, $classes);
            if (is_null($row->visible)) $row->visible = 0;
            $row->pos = $this->_getParam('pos');
            $row->save();
            $id = $row->id;
            $where['component_id = ?'] = $this->_getParam('componentId');

            // Hack für weiterleiten auf Edit-Seite
            $name = Vpc_Abstract::getSetting($this->_getParam('class'), 'componentName');
            $name = str_replace('.', ' -> ', $name);
            $this->view->id = $row->id;
            //wird des braucht? $this->view->componentClass = $classes[$row->component];
            //wird des braucht? $this->view->componentName = $name;

            $this->view->componentConfigs = array();
            $this->view->editComponents = array();
            $extConfig = Vps_Component_Abstract_ExtConfig_Abstract::getInstance($classes[$row->component]);
            $this->view->openConfigKey = $extConfig->getEditAfterCreateConfigKey();
            $cfg = $extConfig->getConfig(Vps_Component_Abstract_ExtConfig_Abstract::TYPE_DEFAULT);
            foreach ($cfg as $k=>$i) {
                $this->view->componentConfigs[$classes[$row->component].'-'.$k] = $i;
                $this->view->editComponents[] = array(
                    'componentClass' => $classes[$row->component],
                    'type' => $k
                );
            }
        } else {
            throw new Vps_Exception("Component $class not found");
        }
    }
    protected function _preforeAddParagraph($row)
    {
        $row->component_id = $this->_getParam('componentId');
    }

    public function jsonCopyAction()
    {
        $id = $this->_getParam('componentId').'-'.$this->_getParam('id');
        if (!Vps_Component_Data_Root::getInstance()->getComponentByDbId($id, array('ignoreVisible'=>true))) {
            throw new Vps_Exception("Component with id '$id' not found");
        }
        $session = new Zend_Session_Namespace('Vpc_Paragraphs:copy');
        $session->id = $id;
    }

    public function jsonCopyAllAction()
    {
        $id = $this->_getParam('componentId');
        if (!Vps_Component_Data_Root::getInstance()->getComponentByDbId($id, array('ignoreVisible'=>true))) {
            throw new Vps_Exception("Component with id '$id' not found");
        }
        $session = new Zend_Session_Namespace('Vpc_Paragraphs:copy');
        $session->id = $id;
    }

    public function jsonPasteAction()
    {
        $session = new Zend_Session_Namespace('Vpc_Paragraphs:copy');
        $id = $session->id;
        if (!$id || !Vps_Component_Data_Root::getInstance()->getComponentByDbId($id, array('ignoreVisible'=>true))) {
            throw new Vps_Exception_Client(trlVps('Clipboard is empty'));
        }
        $target = Vps_Component_Data_Root::getInstance()->getComponentByDbId($this->_getParam('componentId'), array('ignoreVisible'=>true));
        $source = Vps_Component_Data_Root::getInstance()->getComponentByDbId($id, array('ignoreVisible'=>true));

        $c = $target;
        while ($c->parent) {
            if ($c->dbId == $source->dbId) {
                throw new Vps_Exception_Client(trlVps("You can't paste a paragraph into itself."));
            }
            $c = $c->parent;
        }

        if (is_instance_of($source->componentClass, 'Vpc_Paragraphs_Component')) {
            //a whole paragraphs component is in clipboard
            $sources = $source->getChildComponents(array('generator'=>'paragraphs', 'ignoreVisible'=>true));
        } else {
            //a single paragraph (paragraphs child) is in clipboard
            $sources = array($source);
        }
        $classes = Vpc_Abstract::getChildComponentClasses($target->componentClass, 'paragraphs');



        Vps_Component_ModelObserver::getInstance()->disable(); //This would be slow as hell. But luckily we can be sure that for the new (duplicated) components there will be no view cache to clear.

        $progressBar = new Zend_ProgressBar(
            new Vps_Util_ProgressBar_Adapter_Cache($this->_getParam('progressNum')),
            0, Vps_Util_Component::getDuplicateProgressSteps($source)
        );

        $newPos = $this->_getParam('pos');
        $countDuplicated = 0;
        $errorMsg = false;
        foreach ($sources as $source) {
            $targetCls = false;
            if (isset($classes[$source->row->component])) {
                $targetCls = $classes[$source->row->component];
            }
            if ($source->componentClass != $targetCls) {
                if (Vpc_Abstract::hasSetting($source->componentClass, 'componentName')) {
                    $name = Vpc_Abstract::getSetting($source->componentClass, 'componentName');
                    $errorMsg = trlVps("Can't paste paragraph type '{0}', as it is not avaliable here.", $name);
                } else {
                    $errorMsg = trlVps('Source and target paragraphs are not compatible.');
                }
                continue; //skip this one
            }

            $newParagraph = Vps_Util_Component::duplicate($source, $target, $progressBar);
            $countDuplicated++;

            $row = $newParagraph->row;
            $row->pos = $newPos++;
            $row->visible = false;
            $row->save();
        }
        $progressBar->finish();
        Vps_Component_ModelObserver::getInstance()->enable();

        if (!$countDuplicated && $errorMsg) {
            //if at least one was duplicated show no error, else show one
            throw new Vps_Exception_Client($errorMsg);
        }
    }

    public function jsonMakeAllVisibleAction()
    {
        $id = $this->_getParam('componentId');
        $c = Vps_Component_Data_Root::getInstance()->getComponentByDbId($id, array('ignoreVisible'=>true));
        Vpc_Admin::getInstance($c->componentClass)->makeVisible($c);
    }

    public function openPreviewAction()
    {
        $page = Vps_Component_Data_Root::getInstance()->getComponentByDbId(
            $this->_getParam('componentId'),
            array('ignoreVisible'=>true, 'limit' => 1)
        );
        if (!$page) {
            throw new Vps_Exception_Client(trlVps('Page not found'));
        }
        $previewDomain = Vps_Config_Web::getInstance('preview')->server->domain;
        $href = 'http://' . $previewDomain . $page->url;
        header('Location: '.$href);
        exit;
    }
}
