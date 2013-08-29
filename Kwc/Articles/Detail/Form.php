<?php
class Kwc_Articles_Detail_Form extends Kwf_Form
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
        $this->add(new Kwf_Form_Field_TextArea('teaser', trlKwf('Teaser')))
            ->setWidth(500)
            ->setHeight(100);
        $this->add(new Kwf_Form_Field_DateField('date', trlKwf('Publication')))
            ->setAllowBlank(false);
        $this->add(new Kwf_Form_Field_Select('author_id', trlKwf('Author')))
            ->setAllowBlank(false)
            ->setWidth(200)
            ->setValues(Kwf_Model_Abstract::getInstance('Kwc_Articles_Directory_AuthorsModel')->getRows());

        $v = new Zend_Validate_Regex('/^[0-9]{4}\/[0-9]{2}$/');
        $v->setMessage(trlKwf('Please use this format -> Year / Month'), Zend_Validate_Regex::NOT_MATCH);
        $this->add(new Kwf_Form_Field_TextField('vi_nr', trlKwf('VI-Number')))
            ->setWidth(70)
            ->addValidator($v);

        $this->add(Kwc_Abstract_Form::createComponentFormByDbIdTemplate('article_{0}-previewImage', 'previewImage'));

        $columns = $this->add(new Kwf_Form_Container_Columns('is_top'));
        $col = $columns->add(new Kwf_Form_Container_Column());
        $col->setStyle('margin-left: 0px');
        $col->add(new Kwf_Form_Field_Checkbox('is_top_checked', trlKwf('Hot-topic')));
        $col = $columns->add(new Kwf_Form_Container_Column());
        $col->setLabelWidth(50);
        $col->add(new Kwf_Form_Field_DateField('is_top_expire', trlKwf('Ends at')));

        $this->add(new Kwf_Form_Field_Checkbox('read_required', trlKwf('Required reading')));
        $this->add(new Kwf_Form_Field_Checkbox('only_intern', trlKwf('Only intern')));

        $priority = array();
        for ($i = 1; $i <= 10; $i++) {
            $priority[$i] = $i;
        }
        $this->add(new Kwf_Form_Field_Select('priority', trlKwf('Priority')))
            ->setAllowBlank(false)
            ->setValues($priority)
            ->setWidth(40)
            ->setHelpText(trlKwfStatic('For sorting articles if they have same date'));
     }
}
