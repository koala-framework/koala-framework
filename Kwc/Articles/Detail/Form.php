<?php
class Kwc_Articles_Detail_Form extends Kwf_Form
{
    public function __construct($directoryClass = null)
    {
        $this->setDirectoryClass($directoryClass);
        parent::__construct('details');
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
        $this->add(new Kwf_Form_Field_NumberField('mail_priority', trlKwf('E-Mail priority')))
            ->setAllowDecimals(false)
            ->setWidth(100)
            ->setAllowBlank(false)
            ->setDefaultValue("0");
        $this->add(new Kwf_Form_Field_Select('author_id', trlKwf('Author')))
            ->setAllowBlank(false)
            ->setWidth(200)
            ->setValues(Kwf_Model_Abstract::getInstance('Kwc_Articles_Directory_AuthorsModel')->getRows());

        $v = new Zend_Validate_Regex('/^[0-9]{4}\/[0-9]{2}$/');
        $v->setMessage(trlKwf('Please use this format -> Year / Month'), Zend_Validate_Regex::NOT_MATCH);
        $this->add(new Kwf_Form_Field_TextField('vi_nr', trlKwf('VI-Number')))
            ->setWidth(70)
            ->addValidator($v);

        $this->add(Kwc_Abstract_Form::createComponentFormByDbIdTemplate('kwc_article_{0}-previewImage', 'previewImage'));

        $this->add(new Kwf_Form_Field_Checkbox('is_top', trlKwf('Hot-topic')));
        $this->add(new Kwf_Form_Field_Checkbox('read_required', trlKwf('Required reading')));
        $this->add(new Kwf_Form_Field_Checkbox('only_intern', trlKwf('Only intern')));

        $tagsControllerUrl = Kwc_Abstract_Admin::getInstance('Kwc_Articles_Detail_Component')->getControllerUrl('Tags');
        $s = new Kwf_Model_Select();
        $s->whereEquals('tag_type', 'tag');
        $this->add(new Kwf_Form_Field_SuperBoxSelect('ArticleToTag', 'Tag', trlKwf('Tags')))
            ->setWidth(300)
            ->setListWidth(300)
            ->setChildRowsSelect($s)
            ->setTpl('<tpl for="."><div class="x-combo-list-item">{name} <span style="font-size: 10px; color: gray;">({count_used} '. trlKwf('uses') .')</span></div></tpl>')
            ->setAllowAddNewData(true)
            ->setTriggerAction('all')
            ->setMinChars(1)
            ->setValues($tagsControllerUrl.'/json-data')
            ->setAddNewItemUrl($tagsControllerUrl.'/json-add-item');

        $fs = $this->add(new Kwf_Form_Container_FieldSet(trlKwf('Categories')));
        $model = Kwf_Model_Abstract::getInstance('Kwc_Articles_Directory_CategoriesModel');
        $cols = $fs->add(new Kwf_Form_Container_Columns());
        foreach ($model->getRows($model->select()->whereNull('parent_id')->order('pos')) as $category) {
            $col = $cols->add();
            $s = new Kwf_Model_Select();
            $s->whereEquals('parent_id', $category->id);
            $col->add(new Kwf_Form_Field_MultiCheckbox('Categories', 'Category', $category->name))
                ->setShowCheckAllLinks(false)
                ->setValuesSelect($s);
        }
     }
}
