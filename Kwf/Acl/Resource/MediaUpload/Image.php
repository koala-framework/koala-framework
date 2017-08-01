<?php
class Kwf_Acl_Resource_MediaUpload_Image extends Kwf_Acl_Resource_MediaUpload
{
    public function __construct($resourceId, $maxFilesize = null)
    {
        parent::__construct($resourceId, '^image/.*', '\.(jpe?g|png|gif|JPE?G|PNG|GIF)$', $maxFilesize);
    }
}

