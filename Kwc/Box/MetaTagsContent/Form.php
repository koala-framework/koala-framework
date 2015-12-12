<?php
class Kwc_Box_MetaTagsContent_Form extends Kwc_Abstract_Composite_Form
{
    protected $_createFieldsets = false;
    protected function _initFields()
    {
        $this->add(new Kwf_Form_Field_TextArea('description', 'META Description')) //no trl
            ->setWidth(400)
            ->setHeight(50)
            ->setHelpText(trlKwf('Optional, but important for SEO. Short description of the page content in about 170 characters. Important for Google, Facebook, etc.'));

        $this->add(new Kwf_Form_Field_TextField('og_title', 'Open Graph Title'))
            ->setWidth(400);

        $this->add(new Kwf_Form_Field_TextArea('og_description', 'Open Graph Description'))
            ->setWidth(400)
            ->setHeight(50);
    
        parent::_initFields();

        $this->add(new Kwf_Form_Field_Checkbox('noindex', 'noindex'))
            ->setBoxLabel(trlKwf("Don't index this page by search engines."));

        $this->add(new Kwf_Form_Field_Select('sitemap_priority', trlKwf('Priority')))
            ->setValues(array(
                '0.0' => '0.0 '.trlKwf('Low'),
                '0.1' => '0.1',
                '0.2' => '0.2',
                '0.3' => '0.3',
                '0.4' => '0.4',
                '0.5' => '0.5 '.trlKwf('Standard'),
                '0.6' => '0.6',
                '0.7' => '0.7',
                '0.8' => '0.8',
                '0.9' => '0.9',
                '1.0' => '1.0 '.trlKwf('High'),
            ))
            ->setDefaultValue('0.5');

        $this->add(new Kwf_Form_Field_Select('sitemap_changefreq', trlKwf('Change Frequency')))
            ->setValues(array(
                'always'  => trlKwf('Always'),
                'hourly'  => trlKwf('Hourly'),
                'daily'   => trlKwf('Daily'),
                'weekly'  => trlKwf('Weekly'),
                'monthly' => trlKwf('Monthly'),
                'yearly'  => trlKwf('Yearly'),
                'never'   => trlKwf('Never'),
            ))
            ->setDefaultValue('weekly');

    }
}
