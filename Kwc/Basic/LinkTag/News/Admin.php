<?php
class Kwc_Basic_LinkTag_News_Admin extends Kwc_Basic_LinkTag_Abstract_Admin
    implements Kwf_Component_Abstract_Admin_Interface_DependsOnRow
{
    protected $_prefix = 'news';
    protected $_prefixPlural = 'news';

    private $_duplicated = array();

    public function componentToString(Kwf_Component_Data $data)
    {
        $row = $data->getComponent()->getRow();
        $field = $this->_prefix . '_id';
        $data = Kwf_Component_Data_Root::getInstance()
            ->getComponentByDbId($this->_prefixPlural . '_'.$row->$field, array('subroot' => $data));
        if (!$data) return '';
        return $data->name;
    }

    public function getComponentsDependingOnRow(Kwf_Model_Row_Interface $row)
    {
        // nur bei newsmodel
        if ($row->getModel() instanceof Kwc_News_Directory_Model) {
            $linkModel = Kwf_Model_Abstract::getInstance(
                Kwc_Abstract::getSetting($this->_class, 'ownModel')
            );
            $linkingRows = $linkModel->getRows($linkModel->select()
                ->whereEquals($this->_prefix . '_id', $row->{$row->getModel()->getPrimaryKey()})
            );
            if (count($linkingRows)) {
                $ret = array();
                foreach ($linkingRows as $linkingRow) {
                    $c = Kwf_Component_Data_Root::getInstance()
                        ->getComponentByDbId($linkingRow->component_id);
                    //$c kann null sein wenn es nicht online ist
                    if ($c && $c->componentClass == $this->_class) {
                        $ret[] = $c;
                    }
                }
                return $ret;
            }
        }
        return array();
    }

    public function getCardForms()
    {
        $ret = array();

        foreach ($this->getDirectoryComponentClasses() as $class) {
            $name = Kwf_Trl::getInstance()->trlStaticExecute(Kwc_Abstract::getSetting($class, 'componentName'));
            $name = str_replace('.', ' ', $name);
            $ret[$class] = array(
                'form' => Kwc_Abstract_Form::createComponentForm($this->_class, 'child'),
                'title' => $name
            );
        }

        return $ret;
    }

    public function getDirectoryComponentClasses()
    {
        $classes = Kwc_Abstract::getComponentClassesByParentClass('Kwc_News_Directory_Component');
        foreach ($classes as $class) {
            if (is_instance_of($class, 'Kwc_Events_Directory_Component')) continue;
            $ret[] = $class;
        }
        return $ret;
    }

    public function duplicate($source, $target, Zend_ProgressBar $progressBar = null)
    {
        parent::duplicate($source, $target, $progressBar);
        $this->_duplicated[] = array(
            'source' => $source->componentId,
            'target' => $target->componentId,
        );
    }

    //TODO: reuse code from Link_Intern, but for that we have to inherit Link_Intern_Admin which we don't atm
    public function afterDuplicate($rootSource, $rootTarget)
    {
        parent::afterDuplicate($rootSource, $rootTarget);
        $prefix = $this->_prefix;
        $column = "{$prefix}_id";
        foreach ($this->_duplicated as $d) {
            //modify duplicated links so they point to duplicated page
            //only IF link points to page below $rootSource
            $source = Kwf_Component_Data_Root::getInstance()->getComponentById($d['source'], array('ignoreVisible'=>true));
            $sourceRow = $source->getComponent()->getRow();
            $linkTargetIsBelowRootSource = false;
            foreach (Kwf_Component_Data_Root::getInstance()->getComponentsByDbId($prefix.'_'.$sourceRow->$column, array('ignoreVisible'=>true)) as $sourceLinkTarget) {
                do {
                    if ($sourceLinkTarget->componentId == $rootSource->componentId) {
                        $linkTargetIsBelowRootSource = true;
                        break;
                    }
                } while ($sourceLinkTarget = $sourceLinkTarget->parent);
            }
            if ($linkTargetIsBelowRootSource) {
                //get duplicated link target id from duplicate log
                $sql = "SELECT target_component_id FROM kwc_log_duplicate WHERE source_component_id = ? ORDER BY id DESC LIMIT 1";
                $q = Kwf_Registry::get('db')->query($sql, $prefix.'_'.$sourceRow->$column);
                $q = $q->fetchAll();
                if (!$q) continue;
                $linkTargetId =  $q[0]['target_component_id'];
                $target = Kwf_Component_Data_Root::getInstance()->getComponentById($d['target'], array('ignoreVisible'=>true));
                $targetRow = $target->getComponent()->getRow();
                if (substr($linkTargetId, 0, 5) != $prefix.'_') {
                    throw new Kwf_Exception('invalid target_component_id');
                }
                $targetRow->$column = substr($linkTargetId, 5);
                $targetRow->save();
            }
        }
        $this->_duplicated = array();
    }
}
