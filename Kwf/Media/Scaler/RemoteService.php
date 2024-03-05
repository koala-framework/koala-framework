<?php
class Kwf_Media_Scaler_RemoteService extends Kwf_Media_Scaler_Abstract
{
    public function scale($source, $size, $mimeType, array $options)
    {
        $url = Kwf_Config::getValue('mediaScalerService');
        if (!$url) {
            throw new Kwf_Exception("mediaScalerService config not set");
        }
        $client = new Zend_Http_Client($url);
        $blob = file_get_contents($source);
        Kwf_Util_Upload::onFileRead($source);
        $client->setRawData($blob);
        $client->setParameterGet(array(
            'width'  => $size['width'],
            'height'  => $size['height'],
            'skipCleanup'  => isset($options['skipCleanup']) && $options['skipCleanup'],
            'crop_x' => $size['crop']['x'],
            'crop_y' => $size['crop']['y'],
            'crop_width' => $size['crop']['width'],
            'crop_height' => $size['crop']['height'],
            'mimeType' => $mimeType,
            'rotate' => isset($size['rotate']) ? $size['rotate'] : 0,
            'imageCompressionQuality' => isset($size['imageCompressionQuality']) ? $size['imageCompressionQuality'] : Kwf_Config::getValue('imageCompressionQuality'),
        ));
        $response = $client->request('POST');
        if (!$response->isSuccessful()) {
            throw new Kwf_Exception("Image scale failed");
        }
        return $response->getBody();
    }
}
