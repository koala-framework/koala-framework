<?php
class Kwc_Abstract_Image_Form extends Kwc_Abstract_Composite_Form
{
    protected $_imageUploadFieldClass = 'Kwc_Abstract_Image_ImageUploadField';

    protected function _initFields()
    {

        $this->add($this->_createImageUploadField(Kwc_Abstract::getSetting($this->getClass(), 'imageLabel')));

        if (Kwc_Abstract::getSetting($this->getClass(), 'editFilename') || Kwc_Abstract::getSetting($this->getClass(), 'altText') || Kwc_Abstract::getSetting($this->getClass(), 'titleText')) {
            $fs = $this->add(new Kwf_Form_Container_FieldSet('SEO'));
            $fs->setCollapsible(true);
            $fs->setCollapsed(true);
        }
        if (Kwc_Abstract::getSetting($this->getClass(), 'editFilename')) {
            $fs->add(new Kwf_Form_Field_TextField('filename', trlKwf('Filename')))
                ->setAutoFillWithFilename('filename') //to find it by MultiFileUpload and in JavaScript
                ->setVtype('alphanum')
                ->setWidth(300)
                ->setHelpText(trlKwf('Talking filename ("lorem-ipsum-2015"), hyphens and underscores are allowed.'));
        }
        if (Kwc_Abstract::getSetting($this->getClass(), 'altText')) {
            $fs->add(new Kwf_Form_Field_TextField('alt_text', 'ALT Text')) //no trl
                ->setWidth(300)
                ->setHelpText(trlKwf('Short, meaningful description of the image content.'));
        }
        if (Kwc_Abstract::getSetting($this->getClass(), 'titleText')) {
            $fs->add(new Kwf_Form_Field_TextField('title_text', 'IMG Title')) //no trl
                ->setWidth(300)
                ->setHelpText(trlKwf('Some browsers show the text as a tooltip when the mouse pointer is hovering the image.'));
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
