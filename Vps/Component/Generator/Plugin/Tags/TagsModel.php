<?php
class Vps_Component_Generator_Plugin_Tags_TagsModel extends Vps_Model_Db
{
    protected $_table = 'vpc_tags';
    protected $_dependentModels = array(
        'ComponentsToTags' => 'Vps_Component_Generator_Plugin_Tags_ComponentsToTagsModel'
    );
    protected $_toStringField = 'text';
}
