<?php
class Kwf_Assets_Webpack_Chunk
{
    public static function getContents($chunkName)
    {
        $context = array();
        if (Kwf_Assets_WebpackConfig::getDevServerUrl()) {
            $context['ssl'] = array(
                'verify_peer' => false,
                'verify_peer_name' => false
            );
            $filename = Kwf_Assets_WebpackConfig::getDevServerUrl() . "assets/build/{$chunkName}";
        } else {
            $filename = "build/assets/{$chunkName}";
        }

        return file_get_contents($filename, false, stream_context_create($context));
    }
}
