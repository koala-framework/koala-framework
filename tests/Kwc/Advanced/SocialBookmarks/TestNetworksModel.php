<?php
class Vpc_Advanced_SocialBookmarks_TestNetworksModel extends Vpc_Advanced_SocialBookmarks_NetworksModel
{
    public function __construct(array $config = array())
    {
        $config['proxyModel'] = new Vps_Model_FnFFile(array(
            'primaryKey' => 'id',
            'data' => array(
                array('id'=>1, 'component_id'=>'root-socialBookmarks', 'network_id'=>'facebook'),
                array('id'=>2, 'component_id'=>'root-socialBookmarks', 'network_id'=>'twitter'),
            ),
            'uniqueIdentifier' => get_class($this).'-Proxy'
        ));
        $this->_referenceMap['Field']['refModelClass'] = 'Vpc_Advanced_SocialBookmarks_TestModel';
        parent::__construct($config);
    }
}
