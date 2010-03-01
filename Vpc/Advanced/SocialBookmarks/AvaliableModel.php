<?php
class Vpc_Advanced_SocialBookmarks_AvaliableModel extends Vps_Model_FnF
{
    protected $_toStringField = 'name';
    public function __construct()
    {
        $config = array();
        $config['data'] = array(
            array(
                'id' => 'wong',
                'url' => 'http://www.mister-wong.de/index.php?action=addurl&bm_url={0}',
                'name' => 'Mister Wong'
            ),
            array(
                'id' => 'delicious',
                'url' => 'http://del.icio.us/post?url={0}',
                'name' => 'del.icio.us'
            ),
            array(
                'id' => 'yigg',
                'url' => 'http://yigg.de/neu?exturl={0}',
                'name' => 'Yigg'
            ),
            array(
                'id' => 'webnews',
                'url' => 'http://www.webnews.de/einstellen?url={0}',
                'name' => 'Webnews'
            ),
            array(
                'id' => 'linkarena',
                'url' => 'http://linkarena.com/bookmarks/addlink/?url={0}',
                'name' => 'LinkARENA'
            ),
            array(
                'id' => 'google',
                'url' => 'http://www.google.com/bookmarks/mark?op=add&hl=de&bkmk={0}',
                'name' => 'Google'
            ),
            array(
                'id' => 'digg',
                'url' => 'http://digg.com/register?url={0}',
                'name' => 'Digg'
            ),
        );
        parent::__construct($config);
    }
}
