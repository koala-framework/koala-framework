<?php
class Kwc_Articles_Directory_Model extends Kwf_Model_Db
{
    protected $_table = 'kwc_articles';
    protected $_toStringField = 'title';

    protected $_dependentModels = array(
        'Categories' => 'ArticleToCategory'
    );
     protected $_referenceMap = array(
         'Author' => 'author_id->Kwc_Articles_Directory_AuthorsModel',
     );
}
