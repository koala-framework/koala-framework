<?php
class Kwc_Articles_Directory_FavouritesModel extends Kwf_Model_Db
{
    protected $_table = 'kwc_article_favourites';

    // referenceModel Users has to be set in project
    protected $_referenceMap = array(
        'Acticle' => 'article_id->Kwc_Articles_Directory_Model',
        'User' => 'user_id->Users'
    );
}
