<?php
class Vpc_Basic_Text_BlockStyleController extends Vpc_Basic_Text_InlineStyleController
{
    protected $_stylesFormName = 'Vpc_Basic_Text_BlockStyleForm';

    protected function _initFields()
    {
        parent::_initFields();
        $tag = $this->_form->fields->insertAfter('name', new Vps_Form_Field_Select('tag', trlVps('Tag')))
            ->setValues(array(
                'p'    => trlVps('Normal (p)'),
                'h1'   => trlVps('Überschrift 1 (h1)'),
                'h2'   => trlVps('Überschrift 2 (h2)'),
                'h3'   => trlVps('Überschrift 3 (h3)'),
                'h4'   => trlVps('Überschrift 4 (h4)'),
                'h5'   => trlVps('Überschrift 5 (h5)'),
                'h6'   => trlVps('Überschrift 6 (h6)')
             ))
            ->setAllowBlank(false)
            ->setDefaultValue('p');
    }
}
