<?php
class Kwc_Advanced_SocialBookmarks_NetworksModel extends Kwf_Model_Db_Proxy
{
    protected $_table = 'kwc_socialbookmarks';
    protected $_referenceMap = array(
        'Field' => array(
            'column' => 'component_id',
            'refModelClass' => 'Kwc_Advanced_SocialBookmarks_Model'
        )
    );
}
