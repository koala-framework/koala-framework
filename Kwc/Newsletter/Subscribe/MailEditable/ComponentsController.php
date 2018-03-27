<?php
class Kwc_Newsletter_Subscribe_MailEditable_ComponentsController extends Kwc_Mail_Editable_ComponentsController
{
    protected $_modelName = 'Kwc_Newsletter_Subscribe_MailEditable_ComponentsModel';

    protected function _initColumns()
    {
        parent::_initColumns();
        $this->_columns->add(new Kwf_Grid_Column('preview_controller_url'));
    }
}
