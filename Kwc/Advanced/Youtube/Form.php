<?php
class Kwc_Advanced_Youtube_Form extends Kwc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $validator = new Zend_Validate_Regex(array(
            'pattern' => Kwc_Advanced_Youtube_Component::REGEX
        ));
        $validator->setMessage(trlKwf('No valid youtube url'), Zend_Validate_Regex::NOT_MATCH);
        $this->add(new Kwf_Form_Field_UrlField('url', trlKwf('URL')))
            ->addValidator($validator)
            ->setAllowBlank(false)
            ->setWidth(400);
        if (Kwc_Abstract::getSetting($this->getClass(), 'videoWidth') ==
            Kwc_Advanced_Youtube_Component::USER_SELECT
        ) {
            $cards = new Kwf_Form_Container_Cards('size', trlKwf('Size'));
            $cards->setDefaultValue('fullWidth');
            $cards->setAllowBlank(false);

            $card = $cards->add();
            $card->setTitle(trlKwfStatic('full width'));
            $card->setName('fullWidth');

            $card = $cards->add();
            $card->setTitle(trlKwfStatic('user-defined'));
            $card->setName('custom');
            $card->add(new Kwf_Form_Field_TextField('videoWidth', trlKwf('Width')))
                ->setAllowBlank(false);

            $this->add($cards);
        }
        $this->add(new Kwf_Form_Field_Select('dimensions', trlKwf('Dimension')))
            ->setDefaultValue('16x9')
            ->setValues(array(
                '16x9' => trlStatic('16:9'),
                '4x3' => trlStatic('4:3')
            ));
        $this->add(new Kwf_Form_Field_Checkbox('autoplay', trlKwf('Autoplay')));
    }
}
