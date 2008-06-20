<?php
class Vps_View_Helper_Assets
{
    public function assets($assets)
    {
        $indent = str_repeat(' ', 8);
        $ret = '';
        foreach ($assets['css'] as $file) {
            $ret .= "$indent<link rel=\"stylesheet\" type=\"text/css\" href=\"$file\" />\n";
        }
        foreach ($assets['js'] as $file) {
            $ret .= "$indent<script type=\"text/javascript\" src=\"$file\"></script>\n";
        }
        return $ret;
    }
}

