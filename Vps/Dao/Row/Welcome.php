<?php
class Vps_Dao_Row_Welcome extends Vps_Db_Table_Row_Abstract
{
    protected $_cacheImages = array(
        'login' => array(300, 50, Vps_Media_Image::SCALE_CROP),
        'welcome' => array(300, 100)
    );
}
