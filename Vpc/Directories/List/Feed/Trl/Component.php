<?php
class Vpc_Directories_List_Feed_Trl_Component extends Vpc_Chained_Trl_MasterAsChild_Component
{
    public function getItemDirectory()
    {
        return $this->getData()->parent->getComponent()->getItemDirectory();
    }

    public function getSelect()
    {
        return $this->getData()->parent->getComponent()->getSelect();
    }

    public function getCacheVars()
    {
        $dir = $this->getItemDirectory();
        if (is_string($dir)) {
            $c = Vpc_Abstract::getComponentClassByParentClass($dir);
            $generator = Vps_Component_Generator_Abstract::getInstance($c, 'detail');
        } else {
            $generator = $dir->getGenerator('detail');
        }
        return $generator->getCacheVars($dir instanceof Vps_Component_Data ? $dir : null);
    }
}
