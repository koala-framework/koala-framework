<?php
class Kwc_Directories_TopChoose_DirectoriesController extends Kwf_Controller_Action_Auto_Grid
{
    protected $_permissions = array();

    protected function _initColumns()
    {
        parent::_initColumns();
        $this->_columns->add(new Kwf_Grid_Column('id'));
        $this->_columns->add(new Kwf_Grid_Column('title'));
    }

    protected function _fetchData($order, $limit, $start)
    {
        $ret = array();

        $class = $this->_getParam('class');
        $showDirectoryClass = Kwc_Abstract::getSetting($class, 'showDirectoryClass');

        $root = Kwf_Component_Data_Root::getInstance();
        $subRoot = $root->getComponentByDbId($this->_getParam('componentId'), array('ignoreVisible' => true));

        $directories = $root->getComponentsByClass($showDirectoryClass, array(
            'ignoreVisible' => true,
            'subRoot' => $subRoot
        ));

        foreach ($directories as $directory) {
            $title = $directory->getTitle();
            if (Kwc_Abstract::hasSetting($class, 'componentNameShort')) {
                $name = Kwc_Abstract::getSetting($class, 'componentNameShort');
            } else {
                $name = Kwc_Abstract::getSetting($class, 'componentName');
            }
            $name = Kwf_Trl::getInstance()->trlStaticExecute($name);
            if ($title != $name) $title .= ' - ' . $name;

            $ret[] = array(
                'id' => $directory->dbId,
                'title' => $title
            );
        }
        return $ret;
    }
}
