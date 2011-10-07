<?php
interface Vps_View_MailInterface
{
    public function addImage(Zend_Mime_Part $image);
    public function getAttachImages();
}