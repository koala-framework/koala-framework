<?php
class Vpc_Forum_PageFactory extends Vpc_Abstract_PageFactory
{
    protected $_additionalFactories = array(
        'Vpc_Forum_GroupsPageFactory',
        'Vpc_Forum_UserPageFactory'
    );
}
