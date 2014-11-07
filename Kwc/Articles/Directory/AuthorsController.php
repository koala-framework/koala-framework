<?php
class Kwc_Articles_Directory_AuthorsController extends Kwf_Controller_Action_Auto_Grid
{
    protected $_model = 'Kwc_Articles_Directory_AuthorsModel';
    protected $_buttons = array('save', 'add');

    public function indexAction()
    {
        parent::indexAction();
        $this->view->articlesControllerUrl = Kwc_Admin::getInstance($this->_getParam('class'))->getControllerUrl('AuthorArticles');
        $this->view->xtype = 'kwc.articles.directory.authorsPanel';
    }

    protected function _initColumns()
    {
        $this->_columns->add(new Kwf_Grid_Column('token', trlKwf('Contraction'), 100))
            ->setEditor(new Kwf_Form_Field_TextField());
        $this->_columns->add(new Kwf_Grid_Column('firstname', trlKwf('First name'), 200))
            ->setEditor(new Kwf_Form_Field_TextField());
        $this->_columns->add(new Kwf_Grid_Column('lastname', trlKwf('Last name'), 200))
            ->setEditor(new Kwf_Form_Field_TextField());
        $this->_columns->add(new Kwf_Grid_Column('feedback_email', trlKwf('Feedback E-Mail'), 300))
            ->setEditor(new Kwf_Form_Field_EMailField());
        $this->_columns->add(new Kwf_Grid_Column('articles_count', trlKwf('Articles')));
    }
}
