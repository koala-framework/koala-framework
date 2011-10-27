<?php
class Kwc_Advanced_Amazon_Nodes_Form extends Kwc_Abstract_Form
{
    protected function _initFields()
    {
        $multifields = $this->add(new Kwf_Form_Field_MultiFields('Nodes'));
        $multifields->setReferences(array(
            'columns' => array('component_id'),
            'refColumns' => array('component_id')
        ));
        $multifields->setMinEntries(0);
        $fs = $multifields->fields->add(new Kwf_Form_Container_FieldSet(trlKwf('Node {0}')));
        $fs->fields->add(new Kwf_Form_Field_TextField('name', trlKwf('Name')));
        $fs->fields->add(new Kwf_Form_Field_TextField('node_id', trlKwf('Node-ID')));
        $fs->fields->add(new Kwf_Form_Field_Checkbox('visible', trlKwf('Visible')));


        parent::_initFields();
    }
}
