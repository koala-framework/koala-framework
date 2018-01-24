<?php
class Kwf_Assets_Webpack_Chunk
{
    public static function getContents($chunkName)
    {
        if (Kwf_Assets_WebpackConfig::getDevServerUrl()) {
            $filename = Kwf_Assets_WebpackConfig::getDevServerUrl() . "assets/build/{$chunkName}";
        } else {
            $filename = "build/assets/{$chunkName}";
        }

        return file_get_contents($filename);
    }
}
