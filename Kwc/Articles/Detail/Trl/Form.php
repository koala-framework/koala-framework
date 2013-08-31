<?php
class Kwc_Articles_Detail_Trl_Form extends Kwf_Form
{
    public function __construct($name, $detailClass = null)
    {
        $this->setClass($detailClass);
        parent::__construct($name);
    }

    protected function _initFields()
    {
        parent::_initFields();

        $this->add(new Kwf_Form_Field_TextField('title', trlKwf('Title')))
            ->setAllowBlank(false)
            ->setWidth(500);
        $this->add(new Kwf_Form_Field_ShowField('original_title', trlKwf('Original')))
            ->setData(new Kwf_Data_Trl_OriginalComponentFromData('title'));
        $this->add(new Kwf_Form_Field_TextArea('teaser', trlKwf('Teaser')))
            ->setWidth(500)
            ->setHeight(100);
        $this->add(new Kwf_Form_Field_ShowField('original_teaser', trlKwf('Original')))
            ->setData(new Kwf_Data_Trl_OriginalComponentFromData('teaser'));
        $this->add(new Kwf_Form_Field_ShowField('original_date', trlKwf('Publication')))
            ->setData(new Kwf_Data_Trl_OriginalComponentFromData('date'))
            ->setTpl('{value:date}');
        $this->add(new Kwf_Form_Field_ShowField('original_author_id', trlKwf('Author')))
            ->setData(new Kwc_Articles_Detail_Trl_Data('author_id'));
        $this->add(new Kwf_Form_Field_ShowField('original_vi_nr', trlKwf('VI-Number')))
            ->setData(new Kwf_Data_Trl_OriginalComponentFromData('vi_nr'));
        $this->add(Kwc_Abstract_Form::createChildComponentForm($this->getClass(), '-previewImage'));
     }
}
