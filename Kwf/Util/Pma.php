<?php
class Kwf_Util_Pma
{
    public static function dispatch()
    {
        if (!isset($_SERVER['REDIRECT_URL'])) return;
        if (substr($_SERVER['REDIRECT_URL'], 0, 9) != '/kwf/pma/') return;

        global $dbh,$last_sth,$last_sql,$reccount,$out_message,$SQLq,$SHOW_T;
        global $DB,$sqldr,$is_sht,$xurl;
        global $err_msg,$VERSION,$self;
        global $DBDEF;
        global $D,$BOM,$ex_isgz;
        global $ex_gz,$ex_tmpf;
        global $ex_gz,$ex_tmpf;
        global $LFILE, $insql_done;
        global $MAX_ROWS_PER_PAGE,$page,$is_limited_sql;
        $_SERVER['PHP_SELF'] = '/kwf/pma/';

        $config = Kwf_Registry::get('config');
        if (!$config->pma->enable) {
            throw new Kwf_Exception_NotFound();
        }
        if (!in_array($_SERVER['REMOTE_ADDR'], Kwf_Config::getValueArray('pma.restrictToIp'))) {
            throw new Kwf_Exception_AccessDenied();
        }

        $config = Kwf_Registry::get('dao')->getDbConfig();
        $DBDEF=array(
            'user'=>$config['username'],
            'pwd'=>$config['password'],
            'db'=>$config['dbname'],
            'host'=>$config['host'],
            'port'=>"",#optional
            'chset'=>"utf8",#optional, default charset
        );

        $db = Kwf_Registry::get('db');
        $userModel = Kwf_Registry::get('userModel');
        $role = $userModel->getAuthedUserRole();
        if ($role != 'admin') {
            throw new Kwf_Exception_AccessDenied();
        }

        session_write_close();
        restore_error_handler();
        restore_exception_handler();

        include Kwf_Config::getValue('libraryPath').'/phpminiadmin/1.8.120510/phpminiadmin.php';
        exit;
    }
}
