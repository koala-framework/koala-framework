<?php
class Kwf_Media_Output_Component
{
    /**
     * Helper function that calculates vars needed by components with responsive images
     */
    public static function getResponsiveImageVars($dimensions, $imageFile)
    {
        $ret = array();
        $width = 0;
        $aspectRatio = 0;
        $ret['minWidth'] = 0;
        $ret['maxWidth'] = 0;
        if (isset($dimensions['width']) && $dimensions['width'] > 0) {
            $aspectRatio = $dimensions['height'] / $dimensions['width'] * 100;
            $width = $dimensions['width'];
            $ret['widthSteps'] = Kwf_Media_Image::getResponsiveWidthSteps($dimensions, $imageFile);
        }
        $ret['width'] = $width;
        $ret['aspectRatio'] = $aspectRatio;
        return $ret;
    }

    /**
     * Helper function that returns scaled and croped images for media output
     *
     * Used by image components in getMediaOutput.
     * Tries to avoid scaling if not required (to keep gif animation intact)
     */
    public static function getMediaOutputForDimension($data, $dim, $type)
    {
        if (isset($data['url'])) {
            $file = Kwf_Config::getValue('mediaCacheDir').'/remotefile_' . md5($data['url']);
            if (!is_file($file)) {
                $httpClientConfig = array(
                    'timeout' => 20,
                    'persistent' => false
                );
                if (extension_loaded('curl')) {
                    $httpClientConfig['adapter'] = 'Zend_Http_Client_Adapter_Curl';
                } else if (Kwf_Config::getValue('http.proxy.host')) {
                    $httpClientConfig['adapter'] = 'Zend_Http_Client_Adapter_Proxy';
                }
                if (Kwf_Config::getValue('http.proxy.host')) {
                    $httpClientConfig['proxy_host'] = Kwf_Config::getValue('http.proxy.host');
                    $httpClientConfig['proxy_port'] = Kwf_Config::getValue('http.proxy.port');
                }
                $httpClient = new Zend_Http_Client($data['url'], $httpClientConfig);
                $request = $httpClient->request();
                if ($request->getStatus() == 200) {
                    file_put_contents($file, $request->getBody());
                    if (!getimagesize($file)) {
                        unlink($file);
                        throw new Kwf_Exception('File is no image: ' . $data['url']);
                    }
                } else {
                    throw new Kwf_Exception('Could not download file: ' . $data['url']);
                }
            }
            $data['file'] = $file;
        }

        $sourceSize = null;
        if (isset($data['dimensions'])) {
            $sourceSize = $data['dimensions'];
        } else if (isset($data['image'])) {
            $sourceSize = array(
                'width' => $data['image']->getImageWidth(),
                'height' => $data['image']->getImageHeight()
            );
        } else if (isset($data['file'])) {
            if (!is_string($data['file'])) {
                throw new Kwf_Exception("file must be a string (filename) or dimensions must be passed");
            }
            $s = @getimagesize($data['file']);
            $sourceSize = array(
                'width' => $s[0],
                'height' => $s[1],
            );
            unset($s);
        }

        // calculate output width/height on base of getImageDimensions and given width
        $width = substr($type, strlen(Kwf_Media::DONT_HASH_TYPE_PREFIX));
        $width = substr($width, 0, strpos($width, '-'));

        if ($width) {
            $width = Kwf_Media_Image::getResponsiveWidthStep($width, Kwf_Media_Image::getResponsiveWidthSteps($dim, $sourceSize));
            $dim['height'] = $width / $dim['width'] * $dim['height'];
            $dim['width'] = $width;
        }

        $ret = array();
        if (isset($data['image'])) {
            $output = Kwf_Media_Image::scale($data['image'], $dim);
            $ret['contents'] = $output;
        } else {
            $scalingNeeded = true;
            $resultingSize = Kwf_Media_Image::calculateScaleDimensions($sourceSize, $dim);
            if ($sourceSize
                && array($resultingSize['crop']['width'], $resultingSize['crop']['height'])
                    == array($sourceSize['width'], $sourceSize['height'])
                && array($resultingSize['width'], $resultingSize['height'])
                    == array($sourceSize['width'], $sourceSize['height'])
            ) {
                $scalingNeeded = false;
            }
            if ($scalingNeeded) {
                //NOTE: don't pass actual size of the resulting image, scale() will calculate that on it's own
                //else size is calculated twice and we get rounding errors
                $uploadId = isset($data['uploadId']) ? $data['uploadId'] : null;
                $output = Kwf_Media_Image::scale($data['file'], $dim, $uploadId, $sourceSize, $data['mimeType']);
                $ret['contents'] = $output;
            } else {
                $ret['file'] = $data['file'];
            }
        }
        $ret['mimeType'] = $data['mimeType'];

        if ($data['file'] instanceof Kwf_Uploads_Row) {
            $file = $data['file']->getFileSource();
        } else {
            $file = $data['file'];
        }
        $ret['mtime'] = filemtime($file);
        if (isset($data['lifetime'])) {
            $ret['lifetime'] = $data['lifetime'];
        }
        return $ret;
    }

    /**
     * Checks if given type (starting with Kwf_Media::DONT_HASH_TYPE_PREFIX) should
     * return an image by checking Kwf_Media_Image::getResponsiveWidthSteps of image
     */
    public static function isValidImage($id, $type, $className)
    {
        $isValid = Kwf_Media_Output_Component::isValid($id);
        if ($isValid == Kwf_Media_Output_IsValidInterface::VALID
            || $isValid == Kwf_Media_Output_IsValidInterface::VALID_DONT_CACHE
        ) {
            // Can be searched with ignore-visible because if it is invisble and
            // not allowed to show Kwf_Media_Output_Component::isValid would return
            // invalid or access_denied
            $c = Kwf_Component_Data_Root::getInstance()->getComponentById($id, array('ignoreVisible' => true));
            if (!$c) return Kwf_Media_Output_IsValidInterface::INVALID;
            if ($c->componentClass != $className) return Kwf_Media_Output_IsValidInterface::INVALID;
            $baseType = $c->getComponent()->getBaseType();
            $dim = $c->getComponent()->getImageDimensions();
            $imageData = $c->getComponent()->getImageDataOrEmptyImageData();
            $widths = Kwf_Media_Image::getResponsiveWidthSteps($dim, $imageData['dimensions']);
            $ok = false;
            foreach ($widths as $w) {
                if (str_replace('{width}', $w, $baseType) == $type) {
                    $ok = true;
                    break;
                }
            }
            if (!$ok) {
                $isValid = Kwf_Media_Output_IsValidInterface::INVALID;
            }
        }
        return $isValid;
    }

    /**
     * Helper function that can be used in Component implementing Kwf_Media_Output_IsValidInterface
     * to check if the component is visible to the current user
     */
    public static function isValid($id)
    {
        $writeCache = false;
        $cacheId = 'media-isvalid-component-'.$id;
        $plugins = Kwf_Cache_Simple::fetch($cacheId, $success);
        if ($success) {
            //success means it's VALID and we successfully fetched the $plugins
            $ret = Kwf_Media_Output_IsValidInterface::VALID;
        } else {
            $ret = Kwf_Media_Output_IsValidInterface::VALID;
            $c = Kwf_Component_Data_Root::getInstance()->getComponentById($id);
            if (!$c) {
                $c = Kwf_Component_Data_Root::getInstance()->getComponentById($id, array('ignoreVisible'=>true));
                if (!$c) return Kwf_Media_Output_IsValidInterface::INVALID;
                if (Kwf_Component_Data_Root::getShowInvisible()) {
                    //preview im frontend
                    $ret = Kwf_Media_Output_IsValidInterface::VALID_DONT_CACHE;
                } else if (Kwf_Registry::get('acl')->isAllowedComponentById($id, $c->componentClass, Kwf_Registry::get('userModel')->getAuthedUser())) {
                    //paragraphs preview in backend
                    $ret = Kwf_Media_Output_IsValidInterface::VALID_DONT_CACHE;
                } else if (Kwf_Registry::get('acl')->isAllowedUser(Kwf_Registry::get('userModel')->getAuthedUser(), 'kwf_component_preview', 'view')) {
                    //perview user in frontend
                    $ret = Kwf_Media_Output_IsValidInterface::VALID_DONT_CACHE;
                } else {
                    return Kwf_Media_Output_IsValidInterface::ACCESS_DENIED;
                }
            }
            //$ret can be VALID or VALID_DONT_CACHE at this point

            $plugins = array();
            $onlyInherit = false;
            while ($c) {
                $p = Kwc_Abstract::getSetting($c->componentClass, 'pluginsInherit');
                if (!$onlyInherit) {
                    $p = array_merge($p, Kwc_Abstract::getSetting($c->componentClass, 'plugins'));
                }
                foreach ($p as $plugin) {
                    if (is_instance_of($plugin, 'Kwf_Component_Plugin_Interface_Login')) {
                        $plugins[] = array(
                            'plugin' => $plugin,
                            'id' => $c->componentId
                        );
                    }
                }
                if ($c->isPage) {
                    $onlyInherit = true;
                }
                $c = $c->parent;
            }

            if ($ret == Kwf_Media_Output_IsValidInterface::VALID) {
                //only cache VALID, VALID_DONT_CACHE can't be cached
                $writeCache = true;
            }
        }

        foreach ($plugins as $p) {
            $plugin = $p['plugin'];
            $plugin = new $plugin($p['id']);
            if ($plugin->isLoggedIn()) {
                $ret = Kwf_Media_Output_IsValidInterface::VALID_DONT_CACHE;
            } else {
                $c = Kwf_Component_Data_Root::getInstance()->getComponentById($id);
                $userModel = Kwf_Registry::get('userModel');
                if (Kwf_Registry::get('acl')->isAllowedComponentById($id, $c->componentClass, $userModel ? $userModel->getAuthedUser() : null)) {
                    //allow preview in backend always
                    $ret = Kwf_Media_Output_IsValidInterface::VALID_DONT_CACHE;
                } else {
                    $ret = Kwf_Media_Output_IsValidInterface::ACCESS_DENIED;
                     break;
                }
            }
        }

        if ($writeCache && $ret == Kwf_Media_Output_IsValidInterface::VALID_DONT_CACHE) {
            //only cache VALID_DONT_CACHE, not VALID as it will be cached in Kwf_Media::getOutput
            //this cache doesn't need to be cleared, because of it's lifetime
            Kwf_Cache_Simple::add($cacheId, $plugins, 60*60-1); //one second less than isValid cache in Kwf_Media
        }
        return $ret;
    }
}
