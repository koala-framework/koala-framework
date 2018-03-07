<?php
class Kwf_Exception_Logger_LogFiles extends Kwf_Exception_Logger_Abstract
{
    public function log(Kwf_Exception_Abstract $exception, $type, $content)
    {
        $path = 'log/'.$type.'/' . date('Y-m-d');
        $filename = date('H_i_s') . '_' . uniqid() . '.txt';
        $exception->setLogId($type.':'.$filename);

        if (!is_dir($path)) mkdir($path, 0777);
        try {
            $fp = fopen("$path/$filename", 'a');
            fwrite($fp, $content);
            fclose($fp);
        } catch(Exception $e) {
            $to = array();
            if (Kwf_Registry::get('config')->developers) {
                foreach (Kwf_Registry::get('config')->developers as $dev) {
                    if (isset($dev->sendException) && $dev->sendException) {
                        $to[] = $dev->email;
                    }
                }
            }
            if ($to) {
                mail(implode(', ', $to),
                    'Error while trying to write error file',
                    $e->__toString()."\n\n---------------------------\n\nOriginal Exception:\n\n".$content
                    );
            }
        }
        return true;
    }
}
