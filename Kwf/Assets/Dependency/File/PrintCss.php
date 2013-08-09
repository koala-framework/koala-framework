<?php
class Kwf_Assets_Dependency_File_PrintCss extends Kwf_Assets_Dependency_File
{
    public function getMimeType()
    {
        return 'text/css; media=print';
    }
}
