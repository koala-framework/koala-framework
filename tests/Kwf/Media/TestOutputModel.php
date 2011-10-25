<?php
class Kwf_Media_TestOutputModel extends Kwf_Model_FnF implements Kwf_Media_Output_Interface
{
    public static function getMediaOutput($id, $type, $className)
    {
        return array(
            'contents' => '',
            'mimeType' => 'image/jpeg'
        );
    }
}
