<?php
interface Vps_Http_Requestor_Response_Interface
{
    public function getBody();
    public function getStatusCode();
    public function getContentType();
}
