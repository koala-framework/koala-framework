<?php
class Vpc_Posts_Write_Component extends Vpc_Formular_Component
{
    private $_previewComponent;

    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['childComponentClasses']['preview'] = 'Vpc_Posts_Write_Preview_Component';
        $ret['childComponentClasses']['success'] = 'Vpc_Posts_Write_Success_Component';
        $ret['postsTableName'] = 'Vpc_Posts_Model';

        $ret['placeholder']['infotext'] = trlVps('Please write friendly in your posts. Every author is liable for the content of his/her posts. Offending posts will be deleted without a comment.');
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        if (isset($_POST['preview'])) {
            $ret['sent'] = 4;
            //TODO das nicht mit zahlen machen
        }
        $ret['preview'] = $this->_getPreviewComponent()->getTemplateVars();
        $ret['lastPosts'] = array();

        //todo gehört hier eigentlich nicht her:
        if ($this->getParentComponent() instanceof Vpc_Posts_Component) {
            foreach ($this->getParentComponent()->getLastPosts() as $comp) {
                $ret['lastPosts'][] = $comp->getTemplateVars();
            }
        }
        return $ret;
    }

    protected function _getInitContent()
    {
        $initContent = '';
        if ($this->_getParam('edit')) {
            $tableName = $this->_getSetting('postsTableName');
            $postsTable = new $tableName(array('componentClass'=>''));
            $postRow = $postsTable->find($this->_getParam('edit'))->current();
            $initContent = $postRow->content;
        }
        return $initContent;
    }

    protected function _init()
    {
        parent::_init();
        /*
        //name+email auskommentiert
        //später mal in eine unterklasse von dieser verschieben
        //für webs wo man als nicht-eingeloggter user auch posten kann
        $c = $this->_createFieldComponent('Textbox', array('name'=>'name', 'width'=>200));
        $c->store('name', 'name');
        $c->store('fieldLabel', 'Name');
        $c->store('isMandatory', true);

        $c = $this->_createFieldComponent('Textbox', array('name'=>'email',
                                            'width'=>200,
                                            'validator'=>'Zend_Validate_EmailAddress'));
        $c->store('name', 'email');
        $c->store('fieldLabel', 'E-Mail');
        $c->store('isMandatory', true);
        */

        $initContent = $this->_getInitContent();

        $c = $this->_createFieldComponent('Textarea',
            array('name'=>'content', 'width'=>470, 'height'=>150, 'value' => $initContent)
        );
        $c->store('name', 'content');
        $c->store('fieldLabel', trlVps('Please enter the desired text. HTML is not allowed an will be filtered. Links like http://... or www.... will be linked automatically.'));
        $c->store('isMandatory', false);
        $this->_paragraphs[] = $c;

        $c = $this->_createFieldComponent('ShowText',
            array('name'=>'infotext', 'value' => $this->_getPlaceholder('infotext'))
        );
        $c->store('name', 'infotext');
        $c->store('isMandatory', false);
        $this->_paragraphs[] = $c;

        $c = $this->_createFieldComponent('Submit', array('name'=>'preview', 'width'=>150, 'text' => 'Vorschau anzeigen'));
        $c->store('name', 'preview');
        $c->store('fieldLabel', '&nbsp;');
        $this->_paragraphs[] = $c;

        if (isset($_POST['preview'])) {
            $c = $this->_createFieldComponent('Submit', array('name'=>'sbmt', 'width'=>150, 'text' => 'Nachricht absenden'));
            $c->store('name', 'sbmt');
            $c->store('fieldLabel', '&nbsp;');
            $this->_paragraphs[] = $c;
        }
    }

    protected function _beforeSave($row)
    {
    }

    protected function _getValues()
    {
        //TODO, ist a schas hier, überschreibt _getValues von überklasse
        //die a bissi anders arbeiteit (whyever)
        $ret = array();
        foreach ($this->getChildComponents() as $c) {
            if ($c instanceof Vpc_Formular_Field_Interface) {
                $name = $c->getStore('name');
                $ret[$name] = $c->getValue();
            }
        }
        return $ret;
    }

    protected function _processForm()
    {
        if (!isset($_POST['preview']) && isset($_POST['sbmt'])) {
            $tableName = $this->_getSetting('postsTableName');
            // todo: komponente holen un getTable() machen => auf treeComponentCache warten
            $t = new $tableName(array('componentClass' => ''));

            $edit = false;
            if (!$this->_getParam('edit')) {
                $row = $t->createRow();
            } else {
                $postRow = $t->find($this->_getParam('edit'))->current();
                if ($this->getParentComponent()->getChildComponentByRow($postRow)->mayEditPost()) {
                    $row = $postRow;
                    $edit = true;
                } else {
                    $row = $t->createRow();
                }
            }
            $values = $this->_getValues();
            $row->content = $values['content'];
            $row->component_id = $this->getParentComponent()->getDbId();
            $row->visible = 1;
            if (!$edit && Zend_Registry::get('userModel')->getAuthedUser()) {
                $row->user_id = Zend_Registry::get('userModel')->getAuthedUser()->id;
            }
            $this->_beforeSave($row);
            $row->save();
        } else {
            // wird in parentComponent in getTemplateVars gefangen.
            // ist leer damit kein fehler ausgegeben wird
            throw new Vps_ClientException();
        }
    }

    protected function _createFieldComponent($class, $row)
    {
        if (!class_exists($class)) {
            $class = "Vpc_Formular_{$class}_Component";
        }
        $c = new $class($this->getDao(), (object)$row, $this->getDbId(), $this->getPageCollection());
        $c->store('noCols', false);
        $c->store('isValid', true);
        $c->store('isMandatory', false);
        $c->store('fieldLabel', '');
        return $c;
    }

    public function getChildComponents()
    {
        $childComponents = parent::getChildComponents();
        $childComponents[] = $this->_getPreviewComponent();
        return $childComponents;
    }

    protected function _getPreviewComponent()
    {
        if (!isset($this->_previewComponent)) {
            $classes = $this->_getSetting('childComponentClasses');
            $this->_previewComponent = $this->createComponent($classes['preview'], 'preview');
        }
        return $this->_previewComponent;
    }

    public function getEditUrl($postId)
    {
        return $this->getUrl().'?edit='.$postId;
    }
}
