<?php
interface Kwf_View_MailInterface
{
    public function addImage(Zend_Mime_Part $image);
    public function getAttachImages();
}