<?php
class Vps_View_Smarty_Ext extends Vps_View_Smarty
{
    public function __construct($files, $class, $config = array())
    {
        if (!is_array($files)) {
            throw new Vps_View_Exception('Files must be an array.');
        }
        
        if (!is_string($class)) {
            throw new Vps_View_Exception('Class must be a string.');
        }
        

        // Pfade zu den Files hinzufügen
        if (preg_match('#/www/usr/([0-9a-z]+)/#', __FILE__, $m)) {
            $user = $m[1];
        } else if (substr(__FILE__, strlen('/www/public/')) == '/www/public/') {
            $user = 'vivid';
        } else {
            $user = 'production';
        }
        $cfg = new Zend_Config_Ini('../application/config.ini', $user);
        foreach ($files as $key => $file) {
            $files[$key] = $cfg->path->vps->http . $file;
        }

        // Ext-Pfad
        if (isset($cfg->path->vps->ext)) {
            $libraryDomain = $cfg->path->vps->ext;
        } else {
            $libraryDomain = $cfg->path->vps->http;
        }

        // View einrichten
        parent::__construct(VPS_PATH . '/views');
        $this->assign('files', $files);
        $this->assign('class', $class);
        $this->assign('noHead', false);
        $this->assign('libraryDomain', $libraryDomain);
        $this->assign('config', Zend_Json::encode($config));
    }

    public function render($name)
    {
        return $this->_smarty->fetch('Ext.html');
    }
}
