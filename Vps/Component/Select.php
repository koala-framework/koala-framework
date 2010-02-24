<?php
class Vps_Component_Select extends Vps_Model_Select
{
    const WHERE_PAGE = 'wherePage';
    const WHERE_PSEUDO_PAGE = 'wherePseudoPage';
    const WHERE_BOX = 'whereBox';
    const WHERE_MULTI_BOX = 'whereMultiBox';
    const WHERE_FLAGS = 'whereFlags';
    const WHERE_GENERATOR_FLAGS = 'whereGeneratorFlags';
    const WHERE_INHERIT = 'whereInherit';
    const WHERE_UNIQUE = 'whereUnique';
    const WHERE_HAS_EDIT_COMPONENTS = 'whereHasEditComponents';
    const WHERE_GENERATOR = 'whereGenerator';
    const WHERE_GENERATOR_CLASS = 'whereGeneratorClass';
    const WHERE_COMPONENT_KEY = 'whereComponentKey';
    const WHERE_COMPONENT_CLASSES = 'whereComponentClasses';
    const WHERE_FILENAME = 'whereFilename';
    const WHERE_SHOW_IN_MENU = 'whereShowInMenu';
    const WHERE_HOME = 'whereHome';
    const WHERE_PAGE_GENERATOR = 'wherePageGenerator';
    const WHERE_SUBROOT = 'whereSubroot';
    const WHERE_CHILD_OF_SAME_PAGE = 'whereChildOfSamePage';
    const IGNORE_VISIBLE = 'ignoreVisible';

    public function __construct($where = array())
    {
        if (array_key_exists(self::IGNORE_VISIBLE, $where)) {
            $this->ignoreVisible($where[self::IGNORE_VISIBLE]);
            unset($where[self::IGNORE_VISIBLE]);
        }
        parent::__construct($where);
    }

/**
     * @deprecated nur für abwärtskompatibilität
     **/
    public function whereSelect($select)
    {
        foreach ($select->getParts() as $type=>$part) {
            $this->setPart($type, $part);
        }
    }

    public function joinWithParentGenerators($data, $generatorNames)
    {
        foreach ($generatorNames as $generatorName) {
            $data = $data->parent;
            $generator = $data->getGenerator($generatorName);
            $generator->joinWithParentGenerator($this, $data);
        }
    }

    public function wherePageGenerator($value = true)
    {
        $this->_parts[self::WHERE_PAGE_GENERATOR] = $value;
        return $this;
    }

    public function wherePage($value = true)
    {
        $this->_parts[self::WHERE_PAGE] = $value;
        return $this;
    }

    public function wherePseudoPage($value = true)
    {
        $this->_parts[self::WHERE_PSEUDO_PAGE] = $value;
        return $this;
    }

    public function whereBox($value = true)
    {
        $this->_parts[self::WHERE_BOX] = $value;
        return $this;
    }

    public function whereMultiBox($value = true)
    {
        $this->_parts[self::WHERE_MULTI_BOX] = $value;
        return $this;
    }

    public function whereFlags(array $value)
    {
        $this->_parts[self::WHERE_FLAGS] = $value;
        return $this;
    }

    public function whereFlag($flag, $value = true)
    {
        $this->_parts[self::WHERE_FLAGS][$flag] = $value;
        return $this;
    }

    public function whereGeneratorFlags(array $value)
    {
        $this->_parts[self::WHERE_GENERATOR_FLAGS] = $value;
        return $this;
    }

    public function whereGeneratorFlag($flag, $value = true)
    {
        $this->_parts[self::WHERE_GENERATOR_FLAGS][$flag] = $value;
        return $this;
    }

    public function whereInherit($value = true)
    {
        $this->_parts[self::WHERE_INHERIT] = $value;
        return $this;
    }

    public function whereUnique($value = true)
    {
        $this->_parts[self::WHERE_UNIQUE] = $value;
        if ($value) $this->whereInherit();
        return $this;
    }

    public function whereHasEditComponents($value = true)
    {
        $this->_parts[self::WHERE_HAS_EDIT_COMPONENTS] = $value;
        return $this;
    }

    public function whereGenerator($value)
    {
        $this->_parts[self::WHERE_GENERATOR] = $value;
        return $this;
    }

    public function whereGeneratorClass($value)
    {
        $this->_parts[self::WHERE_GENERATOR_CLASS] = $value;
        return $this;
    }

    public function whereComponentKey($value)
    {
        $this->_parts[self::WHERE_COMPONENT_KEY] = $value;
        return $this;
    }

    public function whereComponentClasses(array $value)
    {
        $this->_parts[self::WHERE_COMPONENT_CLASSES] = $value;
        return $this;
    }

    public function whereComponentClass($value)
    {
        return $this->whereComponentClasses(array($value));
    }

    public function whereFilename($value = true)
    {
        $this->_parts[self::WHERE_FILENAME] = $value;
        return $this;
    }

    public function whereShowInMenu($value = true)
    {
        $this->_parts[self::WHERE_SHOW_IN_MENU] = $value;
        return $this;
    }

    public function whereHome($value = true)
    {
        $this->_parts[self::WHERE_HOME] = $value;
        return $this;
    }

    public function whereSubroot($component)
    {
        if (!is_array($component)) {;
            if (!$component instanceof Vps_Component_Data) {
                throw new Vps_Exception("Subroot has to be a component");
            }
            $where = array();
            while ($component) {
                if (Vpc_Abstract::getFlag($component->componentClass, 'subroot')) {
                    $where[] = $component;
                }
                $component = $component->parent;
            }
        } else {
            $where = $component;
        }
        if (!empty($where)) {
            $this->_parts[self::WHERE_SUBROOT] = $where;
        }
        return $this;
    }

    /**
     * Intern fuer effizientes getRecursiveChildComponents ohne parentData
     **/
    public function whereChildOfSamePage(Vps_Component_Data $page)
    {
        $this->_parts[self::WHERE_CHILD_OF_SAME_PAGE] = $page;
        return $this;
    }

    public function ignoreVisible($value = true)
    {
        $this->_parts[self::IGNORE_VISIBLE] = $value;
        return $this;
    }

    public function getHash()
    {
        $parts = array();
        foreach ($this->getParts() as $key => $part) {
            if ($key == self::WHERE_SUBROOT && !empty($part)) {
                $part = $part[0]->componentId;
            }
            if ($key == self::WHERE_CHILD_OF_SAME_PAGE && !empty($part)) {
                $part = $part->componentId;
            }
            $parts[$key] = $part;
        }
        return serialize($parts);
    }

}
