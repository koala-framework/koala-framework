<?php
class Vpc_Forum_Group_PageFactory extends Vpc_Abstract_PageFactory
{
    protected $_additionalFactories = array(
        'Vpc_Forum_Group_NewThreadPageFactory',
        'Vpc_Forum_Group_ThreadsPageFactory'
    );
}
