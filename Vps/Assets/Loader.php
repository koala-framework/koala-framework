<?php
class Vps_Assets_Loader
{
    static public function load()
    {
        if (!isset($_SERVER['REQUEST_URI'])) return;
        require_once 'Vps/Loader.php';
        Vps_Loader::registerAutoload();
        if (substr($_SERVER['REQUEST_URI'], 0, 8)=='/assets/') {
            $url = substr($_SERVER['REQUEST_URI'], 8);
            if (strpos($url, '?') !== false) {
                $url = substr($url, 0, strpos($url, '?'));
            }
            $dep = new Vps_Assets_Dependencies();

            try {
                Vps_Media_Output::output($dep->getFileContents($url));
            } catch (Vps_Assets_NotFoundException $e) {
                throw new Vps_Exception_NotFound();
            }
        }
    }
}
