<?php
class Kwc_Articles_Directory_ContentSender extends Kwc_Directories_List_ViewAjax_DirectoryContentSender
{
    public function sendContent($includeMaster)
    {
        header('Location: /');
    }
}
