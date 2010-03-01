<?php
class Vpc_Advanced_SocialBookmarks_Form extends Vpc_Abstract_Form
{
    protected function _initFields()
    {
        $multifields = $this->add(new Vps_Form_Field_MultiFields('Networks'));
        /*
        $multifields->setReferences(array(
            'columns' => array('component_id'),
            'refColumns' => array('component_id')
        ));
        */
        $multifields->setMinEntries(0);
        $multifields->fields->add(new Vps_Form_Field_Select('network_id', trlVps('Network')))
            ->setValues(Vps_Model_Abstract::getInstance('Vpc_Advanced_SocialBookmarks_AvaliableModel')->getRows())
            ->setAllowBlank(false);

        parent::_initFields();
    }
}
