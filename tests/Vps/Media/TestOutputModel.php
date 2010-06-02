<?php
class Vps_Media_TestOutputModel extends Vps_Model_FnF implements Vps_Media_Output_Interface
{
    public static function getMediaOutput($id, $type, $className)
    {
        return array(
            'contents' => '',
            'mimeType' => 'image/jpeg'
        );
    }
}
