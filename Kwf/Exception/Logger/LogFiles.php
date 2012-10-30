<?php
class Kwf_Exception_Logger_LogFiles extends Kwf_Exception_Logger_Abstract
{
    public function log(Kwf_Exception_Abstract $exception, $type, $content)
    {
        $path = 'log/'.$type.'/' . date('Y-m-d');
        $filename = date('H_i_s') . '_' . uniqid() . '.txt';

        if (!is_dir($path)) @mkdir($path);
        try {
            $fp = fopen("$path/$filename", 'a');
            fwrite($fp, $content);
            fclose($fp);
        } catch(Exception $e) {
            $to = array();
            foreach (Kwf_Registry::get('config')->developers as $dev) {
                if (isset($dev->sendException) && $dev->sendException) {
                    $to[] = $dev->email;
                }
            }
            mail(implode('; ', $to),
                'Error while trying to write error file',
                $e->__toString()."\n\n---------------------------\n\nOriginal Exception:\n\n".$content
                );
        }
        return true;
    }
}
