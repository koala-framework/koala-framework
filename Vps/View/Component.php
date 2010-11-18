<?php
class Vps_View_Component extends Vps_View
{
    public function init()
    {
        parent::init();
        $this->addScriptPath('application/views');
    }

    public static function renderMasterComponent(Vps_Component_Data $component, $masterTemplate = null, $ignoreVisible = false)
    {
        if (!$masterTemplate) $masterTemplate = 'application/views/master/default.tpl';
        return self::renderComponent($component, $ignoreVisible, $masterTemplate);
    }

    public static function renderComponent($component, $ignoreVisible = false, $masterTemplate = false, array $plugins = array())
    {
        if (Zend_Registry::get('config')->debug->componentCache->disable) {
            $output = new Vps_Component_Output_NoCache();
        } else {
            $output = new Vps_Component_Output_Cache();
        }
        $output->setIgnoreVisible($ignoreVisible);
        return $output->render($component, $masterTemplate, $plugins);
    }

    /**
     * Finds a view script from the available directories.
     *
     * @param $name string The base name of the script.
     * @return void
     */
    protected function _script($name)
    {
        return $name;
    }
}
