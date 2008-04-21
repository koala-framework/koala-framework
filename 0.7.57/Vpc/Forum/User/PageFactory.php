<?php
class Vpc_Forum_User_PageFactory extends Vpc_Abstract_PageFactory
{
    protected $_additionalFactories = array(
        'Vpc_Forum_User_EditPageFactory',
        'Vpc_Forum_User_ViewPageFactory'
    );
}
