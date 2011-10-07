<?php
class Kwf_Component_Generator_Plugin_Tags_TagsModel extends Kwf_Model_Db
{
    protected $_table = 'kwc_tags';
    protected $_dependentModels = array(
        'ComponentsToTags' => 'Kwf_Component_Generator_Plugin_Tags_ComponentsToTagsModel'
    );
    protected $_toStringField = 'text';
}
