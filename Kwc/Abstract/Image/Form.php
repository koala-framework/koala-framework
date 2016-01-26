<?php
class Kwc_Abstract_Image_Form extends Kwc_Abstract_Composite_Form
{
    protected $_imageUploadFieldClass = 'Kwc_Abstract_Image_ImageUploadField';

    protected function _initFields()
    {

        $this->add($this->_createImageUploadField(Kwc_Abstract::getSetting($this->getClass(), 'imageLabel')));

        if (Kwc_Abstract::getSetting($this->getClass(), 'editFilename') || Kwc_Abstract::getSetting($this->getClass(), 'altText')) {
            $fs = $this->add(new Kwf_Form_Container_FieldSet('SEO'));
            $fs->setCollapsible(true);
            $fs->setCollapsed(true);
        }
        if (Kwc_Abstract::getSetting($this->getClass(), 'editFilename')) {
            $fs->add(new Kwf_Form_Field_TextField('filename', trlKwf('Filename')))
                ->setAutoFillWithFilename('filename') //to find it by MultiFileUpload and in JavaScript
                ->setVtype('alphanum')
                ->setWidth(300);
        }
        if (Kwc_Abstract::getSetting($this->getClass(), 'altText')) {
            $fs->add(new Kwf_Form_Field_TextField('alt_text', trlKwf('Alt Text')))
                ->setHelpText(trlKwf('Optional: Describe this image for visually handicapped people and search engines.'))
                ->setWidth(300);
        }
        if (Kwc_Abstract::getSetting($this->getClass(), 'imageCaption')) {
            $this->add(new Kwf_Form_Field_TextField('image_caption', trlKwf('Image caption')))
                ->setWidth(300);
        }

        parent::_initFields();
    }

    protected function _createImageUploadField($imageLabel)
    {
        $cls = $this->_imageUploadFieldClass;
        $imageUploadField = new $cls($imageLabel);
        $dimensions = Kwc_Abstract::getSetting($this->getClass(), 'dimensions');
        foreach ($dimensions as &$dimension) {
            if (isset($dimension['text'])) {
                $dimension['text'] = Kwf_Trl::getInstance()->trlStaticExecute($dimension['text']);
            }
        }
        $imageUploadField->setDimensions($dimensions);
        $imageUploadField
            ->setAllowBlankImage(Kwc_Abstract::getSetting($this->getClass(), 'allowBlank'))
            ->setShowHelptext(Kwc_Abstract::getSetting($this->getClass(), 'showHelpText'))
            ->setPreviewUrl(Kwc_Admin::getInstance($this->getClass())->getControllerUrl('Preview').'/preview-with-crop');
        if (Kwc_Abstract::getSetting($this->getClass(), 'maxResolution')) {
            $imageUploadField->setMaxResolution(Kwc_Abstract::getSetting($this->getClass(), 'maxResolution'));
        }
        return $imageUploadField;
    }

    public function setFieldLabel($label)
    {
        if ($label) {
            $this->fields['Image']->setFieldLabel($label);
        }
        return $this;
    }
}
