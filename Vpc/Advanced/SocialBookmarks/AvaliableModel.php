<?php
class Vpc_Advanced_SocialBookmarks_AvaliableModel extends Vps_Model_FnF
{
    protected $_toStringField = 'name';
    public function __construct()
    {
        $config = array();
        $config['data'] = array(
            array(
                'id' => 'facebook',
                'url' => 'http://www.facebook.com/sharer.php?u={0}',
                'name' => 'Facebook'
            ),
            array(
                'id' => 'myspace',
                'url' => 'https://secure.myspace.com/index.cfm?fuseaction=login.simpleform&featureName=postToV3&dest={0}',
                'name' => 'MySpace'
            ),
            array(
                'id' => 'xing',
                'url' => 'http://www.xing.com/',
                'name' => 'XING'
            ),
            array(
                'id' => 'flickr',
                'url' => 'http://www.flickr.com/',
                'name' => 'Flickr'
            ),
            array(
                'id' => 'twitter',
                'url' => 'http://twitter.com/home?status={0}',
                'name' => 'Twitter'
            ),
            array(
                'id' => 'studivz',
                'url' => 'http://www.studivz.net/Link/Selection/Url/?u={0}',
                'name' => 'StudiVZ'
            ),
            array(
                'id' => 'windowslive',
                'url' => 'https://skydrive.live.com/sharefavorite.aspx%2f.SharedFavorites??marklet=1&mkt=en-us&url={0}',
                'name' => 'Windows Live'
            ),
            array(
                'id' => 'yahoo',
                'url' => 'http://myweb2.search.yahoo.com/myresults/bookmarklet?t={0}',
                'name' => 'Yahoo!'
            ),
            array(
                'id' => 'google',
                'url' => 'http://www.google.com/bookmarks/mark?op=add&hl=de&bkmk={0}',
                'name' => 'Google'
            ),
            array(
                'id' => 'linkarena',
                'url' => 'http://linkarena.com/bookmarks/addlink/?url={0}',
                'name' => 'LinkARENA'
            ),
            array(
                'id' => 'misterwong',
                'url' => 'http://www.mister-wong.de/index.php?action=addurl&bm_url={0}',
                'name' => 'Mister Wong'
            ),
            array(
                'id' => 'delicious',
                'url' => 'http://del.icio.us/post?url={0}',
                'name' => 'del.icio.us'
            ),
            array(
                'id' => 'digg',
                'url' => 'http://digg.com/register?url={0}',
                'name' => 'Digg'
            ),
            array(
                'id' => 'folkd',
                'url' => 'http://www.folkd.com/submit/{0}',
                'name' => 'Folkd'
            ),
            array(
                'id' => 'newsvine',
                'url' => 'http://www.newsvine.com/_tools/seed&save?u={0}',
                'name' => 'Newsvine'
            ),
            array(
                'id' => 'reddit',
                'url' => 'http://reddit.com/submit?url={0}',
                'name' => 'reddit'
            ),
            array(
                'id' => 'stumbleupon',
                'url' => 'http://www.stumbleupon.com/submit?url={0}',
                'name' => 'StumbleUpon'
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
        );
        parent::__construct($config);
    }
}
