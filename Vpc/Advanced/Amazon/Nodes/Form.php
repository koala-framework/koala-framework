<?php
class Vpc_Advanced_Amazon_Nodes_Form extends Vpc_Abstract_Form
{
    protected function _initFields()
    {
        $this->add(new Vps_Form_Field_TextField('associate_tag', trlVps('Associate-Tag')));

        $multifields = $this->add(new Vps_Form_Field_MultiFields('Vpc_Advanced_Amazon_Nodes_NodesModel'));
        $multifields->setReferences(array(
            'columns' => array('component_id'),
            'refColumns' => array('component_id')
        ));
        $multifields->setMinEntries(0);
        $fs = $multifields->fields->add(new Vps_Form_Container_FieldSet(trlVps('Node {0}')));
        $fs->fields->add(new Vps_Form_Field_TextField('name', trlVps('Name')));
        $fs->fields->add(new Vps_Form_Field_TextField('node_id', trlVps('Node-ID')));
        $fs->fields->add(new Vps_Form_Field_Checkbox('visible', trlVps('Visible')));


        parent::_initFields();
    }
}
