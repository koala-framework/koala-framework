<?php
class Vpc_Forum_User_PageFactory extends Vpc_Abstract_PageFactory
{
    protected $_additionalFactories = array(
        'edit' => 'Vpc_Forum_User_EditPageFactory',
        'view' => 'Vpc_Forum_User_ViewPageFactory'
    );
}
