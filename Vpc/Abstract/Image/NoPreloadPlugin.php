<?php
class Vpc_Abstract_Image_NoPreloadPlugin extends Vps_Component_Plugin_View_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['assets']['files'][] = 'vps/Vpc/Abstract/Image/NoPreload.js';
        return $ret;
    }

    public function getExecutionPoint()
    {
        return Vps_Component_Plugin_Interface_View::EXECUTE_AFTER;
    }

    public function processOutput($output)
    {
        if (preg_match('/<img([^>]+)>/i', $output, $matches)) {
            $output = str_replace(
                $matches[0],
                '<input type="hidden" class="preventPreload" value="'
                    .htmlentities($matches[0])
                    .'" />',
                $output
            );
        }
        return $output;
    }
}
