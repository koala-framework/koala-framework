<?php
class Kwc_Advanced_SocialBookmarks_Form extends Kwc_Abstract_Form
{
    protected function _initFields()
    {
        $multifields = $this->add(new Kwf_Form_Field_MultiFields('Networks'));
        /*
        $multifields->setReferences(array(
            'columns' => array('component_id'),
            'refColumns' => array('component_id')
        ));
        */
        $multifields->setMinEntries(0);
        $multifields->fields->add(new Kwf_Form_Field_Select('network_id', trlKwf('Network')))
            ->setValues(Kwf_Model_Abstract::getInstance('Kwc_Advanced_SocialBookmarks_AvaliableModel')->getRows())
            ->setAllowBlank(false);

        parent::_initFields();
    }
}
