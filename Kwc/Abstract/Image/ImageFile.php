<?php
class Kwc_Abstract_Image_ImageFile extends Kwf_Form_Field_File
{
    public function __construct($fieldname = null, $fieldLabel = null)
    {
        parent::__construct($fieldname, $fieldLabel);
        $this->setXtype('kwc.imagefile');
        $this->setAllowOnlyImages(true);
    }

    public function load($row, $postData = array())
    {
        $ret = parent::load($row, $postData);
        $component = Kwf_Component_Data_Root::getInstance()->getComponentByDbId($row->component_id, array('ignoreVisible' => true));
        if ($component) { //component can be non-existent if it's in a not selected card
            if (is_instance_of($component->componentClass, 'Kwc_Abstract_Image_Component')) {
                $contentWidth = null;
                $usesContentWidth = false;
                foreach (Kwc_Abstract::getSetting($component->componentClass, 'dimensions') as $dim) {
                    if (isset($dim['width'])) {
                        if ($dim['width'] == Kwc_Abstract_Image_Component::CONTENT_WIDTH) {
                            $usesContentWidth = true;
                        } else if ($dim['width'] > $contentWidth) {
                            $contentWidth = $dim['width'];
                        }
                    }
                }
                if ($usesContentWidth) {
                    $contentWidth = $component->getComponent()->getMaxContentWidth();
                }
            } else {
                $contentWidth = $component->getComponent()->getContentWidth();
            }
            $ret[$this->getFieldName()]['contentWidth'] = $contentWidth;
        }
        return $ret;
    }
}
