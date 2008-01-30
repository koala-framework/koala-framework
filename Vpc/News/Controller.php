<?php
class Vpc_News_Controller extends Vps_Controller_Action_Auto_Vpc_Grid
{
    protected $_buttons = array(
        'save' => true,
        'delete' => true,
        'reload' => true,
        'add'   => true
    );
    protected $_defaultOrder = array('field' => 'publish_date', 'direction' => 'DESC');
    //protected $_editDialog = array();

    public function _initColumns()
    {
        $this->_columns->add(new Vps_Auto_Grid_Column('title', 'Title', 300));
        $this->_columns->add(new Vps_Auto_Grid_Column_Button('properties', ' ', 20))
            ->setButtonIcon('/assets/silkicons/newspaper.png')
            ->setToolTip('Properties');
        $this->_columns->add(new Vps_Auto_Grid_Column_Button('edit', ' ', 20))
            ->setButtonIcon('/assets/silkicons/newspaper_go.png')
            ->setToolTip('Edit News');
        $this->_columns->add(new Vps_Auto_Grid_Column_Date('publish_date', 'Publish Date'));
        $this->_columns->add(new Vps_Auto_Grid_Column_Date('expiry_date', 'Expiry Date'));
        $this->_columns->add(new Vps_Auto_Grid_Column_Visible());
    }

    public function indexAction()
    {
        $config = Vpc_Admin::getConfig($this->class, $this->componentId);

        $settings = call_user_func(array($this->class, 'getSettings'));
        if (isset($settings['categories']) && count($settings['categories'])) {
            $plugins = array();
            foreach ($settings['categories'] as $katname => $katsettings) {
                if (!isset($settings['childComponentClasses'][$katname])) {
                    throw new Vps_Exception('childComponentClass must be set for key \''.$katname.'\'');
                }
                $pluginName = Vpc_Admin::getComponentFile(
                    $settings['childComponentClasses'][$katname], 'Plugins', 'js', true
                );
                if ($pluginName) {
                    $pluginName = str_replace('_', '.', $pluginName);
                    $plugins[] = $pluginName;
                }
            }
        }
        if (isset($plugins) && count($plugins)) {
            $config['config']['componentPlugins'] = $plugins;
        }
        $this->view->vpc($config);
    }
}
