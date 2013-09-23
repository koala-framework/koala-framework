<?php
/**
 * Grid row that displays an image
 */
class Kwf_Grid_Column_Image extends Kwf_Grid_Column
    implements Kwf_Media_Output_Interface
{
    public function __construct($dataIndex = null, $header = null, $ruleKey = null)
    {
        parent::__construct($dataIndex, $header, 60);
        $this->setRenderer('image');
        $this->setRuleKey($ruleKey);
        $this->setSortable(false);
        $this->setMaxHeight(19);
        $this->setShowHoverImage(false);
    }

    /**
     * Sets the reference from model to uploads model
     *
     * @param string reference name to uploads model
     */
    public function setRuleKey($ruleKey)
    {
        return $this->setProperty('ruleKey', $ruleKey);
    }

    /**
     * Sets the maximum height the image can have. The row will resize to that height.
     *
     * defaults to 19 (the default row height)
     *
     * @param int maximum image height
     */
    public function setMaxHeight($maxHeight)
    {
        return $this->setProperty('maxHeight', $maxHeight);
    }

    /**
     * Sets if this grid cell should have a hover image (in a tooltip)
     *
     * @param bool if a hover image should be shown
     */
    public function setShowHoverImage($showHoverImage)
    {
        return $this->setProperty('showHoverImage', $showHoverImage);
    }

    public function load($row, $role, $info)
    {
        $ret = null;
        $uploadRow = $row->getParentRow($this->getRuleKey());
        if ($uploadRow) {
            $width = $this->getWidth()-10;
            $size = array(
                'width' => $width,
                'height' => $this->getMaxHeight(),
                'bestfit' => true,
            );
            $size = Kwf_Media_Image::calculateScaleDimensions($uploadRow->getFileSource(), $size);
            $ret = array(
                'previewUrl' => Kwf_Media::getUrl(get_class($this), $uploadRow->id,
                                                  'preview-'.$width.'-'.$this->getMaxHeight(),
                                                  $uploadRow),
                'previewHeight' => $size['height'],
                'previewWidth' => $size['width'],
            );
            if ($this->getShowHoverImage()) {
                $size = array(
                    'width' => 250,
                    'height' => 250,
                    'bestfit' => true,
                );
                $size = Kwf_Media_Image::calculateScaleDimensions($uploadRow->getFileSource(), $size);
                $ret['hoverUrl'] = Kwf_Media::getUrl(get_class($this), $uploadRow->id, 'hover', $uploadRow);
                $ret['hoverHeight'] = $size['height'];
                $ret['hoverWidth'] = $size['width'];
            }
        }
        return $ret;
    }

    public static function getMediaOutput($uploadId, $type, $className)
    {
        $uploadRow = Kwf_Model_Abstract::getInstance('Kwf_Uploads_Model')->getRow($uploadId);
        if (preg_match('#^preview-(\d+)-(\d+)$#', $type, $m)) {
            $size = array(
                'width' => $m[1],
                'height' => $m[2],
                'bestfit' => true,
            );
        } else if ($type == 'hover') {
            $size = array(
                'width' => 250,
                'height' => 250,
                'bestfit' => true,
            );
        } else {
            throw new Kwf_Exception_NotFound();
        }
        return array(
            'contents' => Kwf_Media_Image::scale($uploadRow->getFileSource(), $size),
            'mimeType' => $uploadRow->mime_type
        );
    }
}
