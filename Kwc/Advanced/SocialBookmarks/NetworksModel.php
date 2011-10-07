<?php
class Vpc_Advanced_SocialBookmarks_NetworksModel extends Vps_Model_Db_Proxy
{
    protected $_table = 'vpc_socialbookmarks';
    protected $_referenceMap = array(
        'Field' => array(
            'column' => 'component_id',
            'refModelClass' => 'Vpc_Advanced_SocialBookmarks_Model'
        )
    );
}
