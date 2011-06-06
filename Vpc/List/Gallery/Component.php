<?php
abstract class Vpc_List_Gallery_Component extends Vpc_Abstract_List_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['assets']['dep'][] = 'VpsEnlargeNextPrevious';
        $ret['componentName'] = trlVps('Gallery');
        $ret['componentIcon'] = new Vps_Asset('images.png');
        $ret['generators']['child']['component'] = 'Vpc_List_Gallery_Image_Component';
        $ret['cssClass'] = 'webStandard';

        $ret['ownModel'] = 'Vps_Component_FieldModel';

        $ret['extConfig'] = 'Vpc_List_Gallery_ExtConfig';

        // muss im web überschrieben werden, zB:
        // vorsicht: imagesPerLine MUSS auch gesetzt sein
/*        $ret['dimensions'] = array(
            'fullWidth' => array(
                'text' => trlVps('zwei Bilder'),
                'width' => 334,
                'height' => 0,
                'scale' => Vps_Media_Image::SCALE_BESTFIT,
                'imagesPerLine' => 2
            )
        );
*/
        return $ret;
    }

    public static function validateSettings($settings, $componentClass)
    {
        parent::validateSettings($settings, $componentClass);
        if (!Vpc_Abstract::hasSetting($componentClass, 'dimensions')) {
            throw new Vps_Exception("Setting 'dimension' must exist");
        }
        if (!count($settings['dimensions'])) {
            throw new Vps_Exception("At least one dimension must be set");
        }
        foreach ($settings['dimensions'] as $dimKey => $dim) {
            if (empty($dim['imagesPerLine'])) {
                throw new Vps_Exception("Key 'imagesPerLine' must be set for dimension with key '$dimKey' (".$dim['text'].")");
            }
        }
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $dimensions = $this->_getSetting('dimensions');
        $ret['imagesPerLine'] = $dimensions[$this->getVariant()]['imagesPerLine'];
        return $ret;
    }

    public function getVariant()
    {
        $variant = $this->_getRow()->variant;
        if (!$variant) {
            $keys = array_keys($this->_getSetting('dimensions'));
            return $keys[0];
        }
        return $variant;
    }
}
