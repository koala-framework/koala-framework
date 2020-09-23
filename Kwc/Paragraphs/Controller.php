<?php
class Kwc_Paragraphs_Controller extends Kwf_Controller_Action_Auto_Kwc_Grid
{
    protected $_permissions = array(
        'save',
        'delete',
    );
    protected $_position = 'pos';

    protected function _initColumns()
    {
        $this->_columns->add(new Kwf_Grid_Column_Visible());
        $this->_columns->add(new Kwf_Grid_Column('component_class'))
            ->setData(new Kwf_Data_Kwc_ComponentClass($this->_getParam('class')));
        $this->_columns->add(new Kwf_Grid_Column('component_name'))
            ->setData(new Kwf_Data_Kwc_ComponentName($this->_getParam('class')));
        $this->_columns->add(new Kwf_Grid_Column('component_icon'))
            ->setData(new Kwf_Data_Kwc_ComponentIcon($this->_getParam('class')));

        $this->_columns->add(new Kwf_Grid_Column('preview'))
            ->setData(new Kwf_Data_Kwc_Frontend($this->_getParam('class')))
            ->setRenderer('component');
        $this->_columns->add(new Kwf_Grid_Column('device_visible'))
            ->setEditor(new Kwf_Form_Field_TextField());

        foreach (Kwf_Component_Data_Root::getInstance()->getPlugins('Kwf_Component_PluginRoot_Interface_GeneratorProperty') as $plugin) {
            $params = $plugin->getGeneratorProperty(Kwf_Component_Generator_Abstract::getInstance($this->_getParam('class'), 'paragraphs'));
            if ($params) {
                $editor = new Kwf_Form_Field_Select();
                $editor->setValues($params['values'])
                    ->setListWidth(200);
                $this->_columns->add(new Kwf_Grid_Column($params['name']))
                    ->setEditor($editor)
                    ->setData(new Kwf_Component_PluginRoot_GeneratorProperty_Data($plugin));
            }
        }

        $this->_columns->add(new Kwf_Grid_Column('edit_components'))
            ->setData(new Kwf_Data_Kwc_EditComponents($this->_getParam('class')));

    }

    public function jsonDataAction()
    {
        parent::jsonDataAction();
        $this->view->componentConfigs = $this->_columns['edit_components']
                                ->getData()->getComponentConfigs();
        $c = Kwf_Component_Data_Root::getInstance()
            ->getComponentByDbId($this->_getParam('componentId'), array('limit'=>1, 'ignoreVisible'=>true));
        if (!Kwf_Config::getValue('kwc.responsive')) {
            $this->view->contentWidth = $c->getComponent()->getContentWidth();
        }
        $this->view->masterLayoutContexts = $c->getComponent()->getMasterLayoutContexts();

        $this->view->deniedComponentClasses = array();
        foreach (Kwc_Abstract::getChildComponentClasses($this->_getParam('class'), 'paragraphs') as $componentKey=>$componentClass) {
            $ev = new Kwf_Component_Event_Component_FilterAddComponentClass($componentClass, $c);
            Kwf_Events_Dispatcher::fireEvent($ev);
            if ($ev->deny) {
                $this->view->deniedComponentClasses[] = $componentClass;
            }
        }
    }

    public function preDispatch()
    {
        parent::preDispatch();
        $this->_components = array();
        foreach (Kwc_Abstract::getChildComponentClasses($this->_getParam('class'), 'paragraphs') as $c) {
            if (Kwc_Abstract::hasSetting($c, 'componentName')) {
                $name = Kwc_Abstract::getSetting($c, 'componentName');
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
            $paragraphsData = Kwf_Component_Data_Root::getInstance()
                ->getComponentByDbId($this->_getParam('componentId'), array('ignoreVisible'=>true, 'limit'=>1));

            $supportedMasterLayoutContexts = Kwf_Component_Layout_Abstract::getInstance($class)->getSupportedContexts();
            if ($supportedMasterLayoutContexts !== false) {
                $masterLayoutContexts = $paragraphsData->getComponent()->getMasterLayoutContexts();
                foreach ($masterLayoutContexts as $ctx) {
                    if (!in_array($ctx, $supportedMasterLayoutContexts)) {
                        throw new Kwf_Exception("Supported Content Spans doesn't match"); //button is hidden in JS
                    }
                }
            }
            $ev = new Kwf_Component_Event_Component_FilterAddComponentClass($class, $paragraphsData);
            Kwf_Events_Dispatcher::fireEvent($ev);
            if ($ev->deny) {
                throw new Kwf_Exception("Adding '$class' is denied for $paragraphsData->componentId by plugin");
            }

            $row = $this->_model->createRow();
            $this->_preforeAddParagraph($row);
            $generators = Kwc_Abstract::getSetting($this->_getParam('class'), 'generators');
            $classes =$generators['paragraphs']['component'];
            $row->component = array_search($class, $classes);
            if (is_null($row->visible)) $row->visible = 0;
            if (is_null($row->device_visible)) $row->device_visible = 'all';
            $row->pos = $this->_getParam('pos');
            $row->save();
            $id = $row->id;
            $where['component_id = ?'] = $this->_getParam('componentId');

            // Hack für weiterleiten auf Edit-Seite
            $name = Kwc_Abstract::getSetting($this->_getParam('class'), 'componentName');
            $name = str_replace('.', ' -> ', $name);
            $this->view->id = $row->id;
            //wird des braucht? $this->view->componentClass = $classes[$row->component];
            //wird des braucht? $this->view->componentName = $name;

            $this->view->componentConfigs = array();
            $this->view->editComponents = array();
            $extConfig = Kwf_Component_Abstract_ExtConfig_Abstract::getInstance($classes[$row->component]);
            $this->view->openConfigKey = $extConfig->getEditAfterCreateConfigKey();
            $cfg = $extConfig->getConfig(Kwf_Component_Abstract_ExtConfig_Abstract::TYPE_DEFAULT);
            foreach ($cfg as $k=>$i) {
                $this->view->componentConfigs[$classes[$row->component].'-'.$k] = $i;
                $this->view->editComponents[] = array(
                    'componentClass' => $classes[$row->component],
                    'type' => $k
                );
            }
        } else {
            throw new Kwf_Exception("Component $class not found");
        }
    }
    protected function _preforeAddParagraph($row)
    {
        $row->component_id = $this->_getParam('componentId');
    }

    public function jsonCopyAction()
    {
        $id = $this->_getParam('componentId').'-'.$this->_getParam('id');
        if (!Kwf_Component_Data_Root::getInstance()->getComponentByDbId($id, array('ignoreVisible'=>true))) {
            throw new Kwf_Exception("Component with id '$id' not found");
        }
        $session = new Kwf_Session_Namespace('Kwc_Paragraphs:copy');
        $session->id = $id;
    }

    public function jsonCopyAllAction()
    {
        $id = $this->_getParam('componentId');
        if (!Kwf_Component_Data_Root::getInstance()->getComponentByDbId($id, array('ignoreVisible'=>true))) {
            throw new Kwf_Exception("Component with id '$id' not found");
        }
        $session = new Kwf_Session_Namespace('Kwc_Paragraphs:copy');
        $session->id = $id;
    }

    public function jsonPasteAction()
    {
        if (Zend_Registry::get('db')) Zend_Registry::get('db')->beginTransaction();

        $session = new Kwf_Session_Namespace('Kwc_Paragraphs:copy');
        $id = $session->id;
        if (!$id || !Kwf_Component_Data_Root::getInstance()->getComponentByDbId($id, array('ignoreVisible'=>true))) {
            throw new Kwf_Exception_Client(trlKwf('Clipboard is empty'));
        }
        $target = Kwf_Component_Data_Root::getInstance()->getComponentByDbId($this->_getParam('componentId'), array('ignoreVisible'=>true));
        $source = Kwf_Component_Data_Root::getInstance()->getComponentByDbId($id, array('ignoreVisible'=>true));

        $c = $target;
        while ($c->parent) {
            if ($c->dbId == $source->dbId) {
                throw new Kwf_Exception_Client(trlKwf("You can't paste a paragraph into itself."));
            }
            if ($c->isPage) break;
            $c = $c->parent;
        }

        $sourceIsParagraphs = is_instance_of($source->componentClass, 'Kwc_Paragraphs_Component');
        if ($source->isPage && $sourceIsParagraphs) {
            //a whole paragraphs component is in clipboard
            $sources = $source->getChildComponents(array('generator'=>'paragraphs', 'ignoreVisible'=>true));
        } else if (!$source->isPage && !is_instance_of($source->parent->componentClass, 'Kwc_Paragraphs_Component')) {
            //a whole paragraphs component is in clipboard
            $sources = $source->getChildComponents(array('generator'=>'paragraphs', 'ignoreVisible'=>true));
        } else {
            //a single paragraph (paragraphs child) is in clipboard
            $sources = array($source);
        }
        unset($source);
        $classes = Kwc_Abstract::getChildComponentClasses($target->componentClass, 'paragraphs');



        Kwf_Events_ModelObserver::getInstance()->disable(); //This would be slow as hell. But luckily we can be sure that for the new (duplicated) components there will be no view cache to clear.

        $steps = 0;
        foreach ($sources as $s) {
            $steps += Kwf_Util_Component::getDuplicateProgressSteps($s);
        }
        $progressBar = new Zend_ProgressBar(
            new Kwf_Util_ProgressBar_Adapter_Cache($this->_getParam('progressNum')),
            0, $steps
        );

        $newPos = $this->_getParam('pos');
        $countDuplicated = 0;
        $errorMsg = false;
        foreach ($sources as $s) {
            $targetCls = false;
            if (isset($classes[$s->row->component])) {
                $targetCls = $classes[$s->row->component];
            }

            $sourceCls = $s->componentClass;
            $sourceCls = strpos($sourceCls, '.') ? substr($sourceCls, 0, strpos($sourceCls, '.')) : $sourceCls;
            $targetCls = strpos($targetCls, '.') ? substr($targetCls, 0, strpos($targetCls, '.')) : $targetCls;

            //set setting similarComponent to mark target component as copiable
            if ($sourceCls != $targetCls
                && (!Kwc_Abstract::hasSetting($targetCls, 'similarComponent') || Kwc_Abstract::getSetting($targetCls, 'similarComponent') != $s->componentClass)
            ) {
                if (Kwc_Abstract::hasSetting($s->componentClass, 'componentName')) {
                    $name = Kwf_Trl::getInstance()->trlStaticExecute(Kwc_Abstract::getSetting($s->componentClass, 'componentName'));
                    $errorMsg = trlKwf("Can't paste paragraph type '{0}', as it is not avaliable here.", $name);
                } else {
                    $errorMsg = trlKwf('Source and target paragraphs are not compatible.');
                }
                continue; //skip this one
            }

            try {
                $newParagraph = Kwf_Util_Component::duplicate($s, $target, $progressBar);
            } catch (Kwf_Component_Exception_IncompatibleContexts $e) {
                throw new Kwf_Exception_Client(trlKwf("Can't paste paragraph as it's not compatible with this context."));
            }
            $countDuplicated++;

            $row = $newParagraph->row;
            $row->pos = $newPos++;
            $row->visible = false;
            $row->save();

            Kwf_Util_Component::afterDuplicate($s, $target);
        }
        $progressBar->finish();
        Kwf_Events_ModelObserver::getInstance()->enable();

        if (Zend_Registry::get('db')) Zend_Registry::get('db')->commit();

        if (!$countDuplicated && $errorMsg) {
            //if at least one was duplicated show no error, else show one
            throw new Kwf_Exception_Client($errorMsg);
        }
    }

    public function jsonMakeAllVisibleAction()
    {
        $id = $this->_getParam('componentId');
        $c = Kwf_Component_Data_Root::getInstance()->getComponentByDbId($id, array('ignoreVisible'=>true));
        Kwc_Admin::getInstance($c->componentClass)->makeVisible($c);
    }

    public function openPreviewAction()
    {
        $page = Kwf_Component_Data_Root::getInstance()->getComponentByDbId(
            $this->_getParam('componentId'),
            array('ignoreVisible'=>true, 'limit' => 1)
        );
        if (!$page) {
            throw new Kwf_Exception_Client(trlKwf('Page not found'));
        }
        header('Location: '.$page->getPreviewUrl());
        exit;
    }
}
