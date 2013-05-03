<?php
class Kwc_Shop_Products_Detail_Form extends Kwc_Directories_Item_Detail_Form
{
    private $_cards;
    private $_fieldSet;

    protected function _init()
    {
        parent::_init();

        $fs = $this->add(new Kwf_Form_Container_FieldSet(trlKwf('Data')));
        $this->_fieldSet = $fs;
        $fs->add(new Kwf_Form_Field_TextField('title', trlKwf('Title')));
        $fs->add(new Kwf_Form_Field_Checkbox('visible', trlKwf('Visible')));

        $fs->add($this->_createChildComponentForm('-image'));

        $mf = $this->add(new Kwf_Form_Field_MultiFields('Prices'));
        $mf->setModel(Kwf_Model_Abstract::getInstance('Kwc_Shop_ProductPrices'));
        $mf->setPosition(false);
        $fs = $mf->fields->add(new Kwf_Form_Container_FieldSet(trlKwf('Price')));
            $fs->add(new Kwf_Form_Field_NumberField('price', trlKwf('Price')))
                ->setAllowBlank(false);
            $fs->add(new Kwf_Form_Field_DateTimeField('valid_from', trlKwf('Valid From')))
                ->setAllowBlank(false);

        $this->add($this->_createChildComponentForm('-text'));
    }

    public function setModel($model)
    {
        parent::setModel($model);
        if ($this->_cards) {
            foreach ($this->_cards as $c) {
                $c->getIterator()->current()->setModel($model);
            }
        }
    }

    protected function _getFieldSet()
    {
        return $this->_fieldSet;
    }
}
