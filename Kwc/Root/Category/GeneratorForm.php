<?php
class Kwc_Root_Category_GeneratorForm extends Kwf_Form
{
    private $_componentOrParent;
    private $_generator;
    private $_componentField;

    public $toSaveGeneratorProperty = array();

    public function __construct($componentOrParent, $generator)
    {
        $this->_componentOrParent = $componentOrParent;
        $this->_generator = $generator;
        $this->setModel($generator->getModel());
        parent::__construct();
    }

    protected function _init()
    {
        parent::_init();

        $componentClasses = array();
        $componentNames = array();
        $component = $this->_componentOrParent;
        while (empty($componentClasses) && $component) {
            foreach (Kwc_Abstract::getSetting($component->componentClass, 'generators') as $key => $generator) {
                if (is_instance_of($generator['class'], 'Kwc_Root_Category_Generator')) {
                    foreach ($generator['component'] as $k => $class) {
                        $name = Kwf_Trl::getInstance()->trlStaticExecute(Kwc_Abstract::getSetting($class, 'componentName'));
                        if ($name) {
                            $name = str_replace('.', ' ', $name);
                            $componentNames[$k] = $name;
                            $componentClasses[$k] = $class;
                        }
                    }
                }
            }
            $component = $component->parent;
        }

        $fields = $this->fields;
        $fields->add(new Kwf_Form_Field_TextField('name', trlKwf('Name of Page')))
            ->setAllowBlank(false);

        $fs = $fields->add(new Kwf_Form_Container_FieldSet('name', trlKwf('Name of Page')))
            ->setTitle(trlKwf('Custom Filename'))
            ->setCheckboxName('custom_filename')
            ->setCheckboxToggle(true);
        $fs->add(new Kwf_Form_Field_TextField('filename', trlKwf('Filename')))
            ->setAllowBlank(false)
            ->setVtype('alphanum');

        $this->_componentField = $fields->add(new Kwf_Form_Field_Select('component',  trlKwf('Pagetype')))
            ->setValues($componentNames)
            ->setPossibleComponentClasses($componentClasses) //just for PageController
            ->setTpl('<tpl for="."><div class="x2-combo-list-item">{name:htmlEncode}</div></tpl>')
            ->setAllowBlank(false);

        $hideInMenuText = trlKwf('Hide in Menu');
        $fs = $fields;
        if ($this->_generator->getUseMobileBreakpoints()) {
            $fs = $fields->add(new Kwf_Form_Container_FieldSet(trlKwf('Menu settings')));
            $hideInMenuText = trlKwf('Hide');
            $fs->add(new Kwf_Form_Field_Select('device_visible',  trlKwf('Device visible')))
                ->setListWidth(250)
                ->setDefaultValue(Kwf_Component_Data::DEVICE_VISIBLE_ALL)
                ->setValues(array(
                    Kwf_Component_Data::DEVICE_VISIBLE_ALL => trlKwf('show on all devices'),
                    Kwf_Component_Data::DEVICE_VISIBLE_HIDE_ON_MOBILE => trlKwf('hide on mobile devices'),
                    Kwf_Component_Data::DEVICE_VISIBLE_ONLY_SHOW_ON_MOBILE => trlKwf('only show on mobile devices')
                ))
                ->setTpl('<tpl for=".">
                    <div class="x2-combo-list-item">
                        <tpl if="id==\''.Kwf_Component_Data::DEVICE_VISIBLE_ALL.'\'"><img src="/assets/kwf/images/devices/showAll.png" class="left"/></tpl>
                        <tpl if="id==\''.Kwf_Component_Data::DEVICE_VISIBLE_HIDE_ON_MOBILE.'\'"><img src="/assets/kwf/images/devices/smartphoneHide.png" class="left"/></tpl>
                        <tpl if="id==\''.Kwf_Component_Data::DEVICE_VISIBLE_ONLY_SHOW_ON_MOBILE.'\'"><img src="/assets/kwf/images/devices/smartphone.png" class="left"/></tpl>
                        <p class="left" style="margin-left: 3px; line-height: 16px;">{name}</p>
                    </div>
                </tpl>');
        }
        $fs->add(new Kwf_Form_Field_Checkbox('hide',  $hideInMenuText));

        foreach (Kwf_Component_Data_Root::getInstance()->getPlugins('Kwf_Component_PluginRoot_Interface_GeneratorProperty') as $plugin) {
            $params = $plugin->getGeneratorProperty($this->_generator);
            if ($params) {
                $this->add(new Kwf_Form_Field_Select($params['name'],  $params['label']))
                    ->setValues($params['values'])
                    ->setDefaultValue($params['defaultValue'])
                    ->setWidth(300)
                    ->setData(new Kwf_Component_PluginRoot_GeneratorProperty_Data($plugin, $this));
            }
        }
    }

    public function afterSave($parentRow, $postData)
    {
        parent::afterSave($parentRow, $postData);
        foreach ($this->toSaveGeneratorProperty as $i) {
            if (isset($i['row']->component_id)) {
                $id = $i['row']->component_id.'-'.$i['row']->id;
            } else {
                $id = $i['row']->id;
            }
            $data = Kwf_Component_Data_Root::getInstance()->getComponentByDbId($id, array('ignoreVisible'=>true, 'limit'=>1));
            if (!$data) {
                throw new Kwf_Exception("Didn't get data for $id");
            }
            $i['plugin']->saveGeneratorPropertyValue($data, $i['value']);
        }
    }

    public function getComponentField()
    {
        return $this->_componentField;
    }
}
