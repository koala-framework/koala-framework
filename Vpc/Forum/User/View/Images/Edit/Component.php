<?php
class Vpc_Forum_User_View_Images_Edit_Component extends Vpc_Formular_Component
{
    public static function getSettings()
    {
        $ret = array_merge(parent::getSettings(), array(
            'tablename'  => 'Vpc_Formular_Model',
            'hideInNews' => true,
            'fieldsNotSaved' => array('sbmt')
        ));
        $ret['childComponentClasses']['success'] = 'Vpc_User_Edit_Success_Component';
        $ret['componentName'] = '';
        return $ret;
    }

    protected function _init()
    {
        parent::_init();

        $user = $this->_getUserRow();

        $fieldSettings = array('name'  => 'image',
                               'width' => 250);
        $c = $this->_createFieldComponent('FileUpload', $fieldSettings);
        $c->store('name', 'image');
        $c->store('fieldLabel', trlVps('Upload new image'));
        $c->store('isMandatory', true);

        $fieldSettings = array('name'  => 'comment',
                               'width' => 250,
                               'value' => '');
        $c = $this->_createFieldComponent('Textbox', $fieldSettings);
        $c->store('name', 'comment');
        $c->store('fieldLabel', trlVps('Comment'));
        $c->store('isMandatory', false);

        $c = $this->_createFieldComponent('Submit', array(
            'name'=>'sbmt', 'width'=>200, 'text' => trlVps('Upload')
        ));
        $c->store('name', 'sbmt');
        $c->store('fieldLabel', '&nbsp;');
    }

    protected function _getUserRow()
    {
        return Zend_Registry::get('userModel')->getAuthedUser();
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();

        $pageUserId = $this->getParentComponent()
                        ->getParentComponent()->getCurrentPageKey();
        if ($pageUserId != Zend_Registry::get('userModel')->getAuthedUser()->id) {
            //todo: bessere loesung
            die("invalid user");
        }
        $ret['images'] = array();
        foreach ($this->getParentComponent()->getChildComponents() as $i) {
            if (isset($_GET['delete']) && $_GET['delete'] == $i->getCurrentComponentKey()) {
                $image = $i->getImageRow()->findParentRow('Vps_Dao_File');
                $i->getImageRow()->delete();
                $image->delete();
                $this->getParentComponent()->getTable()->find($_GET['delete'])->current()->delete();
            } else {
                $ret['images'][] = array(
                    'image' => $i->getTemplateVars(),
                    'delete' => $this->getUrl().'?delete='.$i->getCurrentComponentKey(),
                    'comment' => $i->getImageRow()->comment
                );
            }
        }


        $ret['formTemplate'] = Vpc_Admin::getComponentFile('Vpc_Formular_Component', '', 'tpl');
        return $ret;
    }

    protected function _processForm()
    {
        $cls = Vpc_Abstract::getSetting(get_class($this->getParentComponent()),
                                    'childComponentClasses');
        $t = new Vpc_Basic_Image_Enlarge_Model(array(
            'componentClass' => $cls['child']
        ));
        $row = $t->createRow();
        foreach ($this->getChildComponents() as $c) {
            if ($c instanceof Vpc_Formular_Field_Interface) {
                $name = $c->getStore('name');
                if ($name == 'image') {
                    $r = $this->getParentComponent()->getTable()->createRow();
                    $r->component_id = $this->getParentComponent()->getId();
                    $r->component_class = $cls['child'];
                    $r->visible = 1;
                    $r->pos = count($this->getParentComponent()->getChildComponents())+1;
                    $r->save();
                    $row->component_id = $r->component_id.'-'.$r->id;
                    $row->vps_upload_id = $c->getValue();
                    $row->enlarge = 1;
                } else if ($name == 'comment') {
                    $row->comment = $c->getValue();
                }
            }
        }
        $row->save();
    }

}
