<?php
class Kwc_Newsletter_SubscribeApi_FrontendForm extends Kwc_Newsletter_Subscribe_FrontendForm
{
    protected $_modelName = null;

    public function __construct($name, $componentClass)
    {
        parent::__construct($name, $componentClass, null);
    }

    protected function _addEmailValidator()
    {
    }
}
