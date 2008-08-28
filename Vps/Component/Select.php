<?php
class Vps_Component_Select extends Vps_Model_Select
{
    const WHERE_PAGE = 'wherePage';
    const WHERE_PSEUDO_PAGE = 'wherePseudoPage';
    const WHERE_BOX = 'whereBox';
    const WHERE_MULTI_BOX = 'whereMultiBox';
    const WHERE_FLAGS = 'whereFlags';
    const WHERE_INHERIT = 'whereInherit';
    const WHERE_UNIQUE = 'whereUnique';
    const WHERE_HAS_EDIT_COMPONENTS = 'whereHasEditComponents';
    const WHERE_GENERATOR = 'whereGenerator';
    const WHERE_COMPONENT_KEY = 'whereComponentKey';
    const WHERE_COMPONENT_CLASSES = 'whereComponentClasses';
    const WHERE_FILENAME = 'whereFilename';
    const WHERE_SHOW_IN_MENU = 'whereShowInMenu';
    const WHERE_HOME = 'whereHome';
    const WHERE_TYPE = 'whereType';
    const IGNORE_VISIBLE = 'ignoreVisible';
    const SKIP_ROOT = 'skipRoot';

    /**
     * @deprecated nur für abwärtskompatibilität
     **/
    public function whereSelect($select)
    {
        foreach ($select->getParts() as $type=>$part) {
            $this->setPart($type, $part);
        }
    }

    public function wherePage($value = true)
    {
        $this->_checkNotProcessed(self::WHERE_PAGE);
        $this->_parts[self::WHERE_PAGE] = $value;
        return $this;
    }

    public function wherePseudoPage($value = true)
    {
        $this->_checkNotProcessed(self::WHERE_PSEUDO_PAGE);
        $this->_parts[self::WHERE_PSEUDO_PAGE] = $value;
        return $this;
    }

    public function whereBox($value = true)
    {
        $this->_checkNotProcessed(self::WHERE_BOX);
        $this->_parts[self::WHERE_BOX] = $value;
        return $this;
    }

    public function whereMultiBox($value = true)
    {
        $this->_checkNotProcessed(self::WHERE_MULTI_BOX);
        $this->_parts[self::WHERE_MULTI_BOX] = $value;
        return $this;
    }

    public function whereFlags(array $value)
    {
        $this->_checkNotProcessed(self::WHERE_FLAGS);
        $this->_parts[self::WHERE_FLAGS] = $value;
        return $this;
    }

    public function whereFlag($flag, $value = true)
    {
        $this->_checkNotProcessed(self::WHERE_FLAGS);
        $this->_parts[self::WHERE_FLAGS][$flag] = $value;
        return $this;
    }

    public function whereInherit($value = true)
    {
        $this->_checkNotProcessed(self::WHERE_INHERIT);
        $this->_parts[self::WHERE_INHERIT] = $value;
        return $this;
    }

    public function whereUnique($value = true)
    {
        $this->_checkNotProcessed(self::WHERE_UNIQUE);
        $this->_parts[self::WHERE_UNIQUE] = $value;
        if ($value) $this->whereInherit();
        return $this;
    }

    public function whereHasEditComponents($value = true)
    {
        $this->_checkNotProcessed(self::WHERE_HAS_EDIT_COMPONENTS);
        $this->_parts[self::WHERE_HAS_EDIT_COMPONENTS] = $value;
        return $this;
    }

    public function whereGenerator($value)
    {
        $this->_checkNotProcessed(self::WHERE_GENERATOR);
        $this->_parts[self::WHERE_GENERATOR] = $value;
        return $this;
    }

    public function skipRoot($value = true)
    {
        $this->_checkNotProcessed(self::SKIP_ROOT);
        $this->_parts[self::SKIP_ROOT] = $value;
        return $this;
    }

    public function whereComponentKey($value)
    {
        $this->_checkNotProcessed(self::WHERE_COMPONENT_KEY);
        $this->_parts[self::WHERE_COMPONENT_KEY] = $value;
        return $this;
    }

    public function whereComponentClasses(array $value)
    {
        $this->_checkNotProcessed(self::WHERE_COMPONENT_CLASSES);
        $this->_parts[self::WHERE_COMPONENT_CLASSES] = $value;
        return $this;
    }

    public function whereComponentClass($value)
    {
        return $this->whereComponentClasses(array($value));
    }

    public function whereFilename($value = true)
    {
        $this->_checkNotProcessed(self::WHERE_FILENAME);
        $this->_parts[self::WHERE_FILENAME] = $value;
        return $this;
    }

    public function whereShowInMenu($value = true)
    {
        $this->_checkNotProcessed(self::WHERE_SHOW_IN_MENU);
        $this->_parts[self::WHERE_SHOW_IN_MENU] = $value;
        return $this;
    }

    public function whereHome($value = true)
    {
        $this->_checkNotProcessed(self::WHERE_HOME);
        $this->_parts[self::WHERE_HOME] = $value;
        return $this;
    }
    public function whereType($value)
    {
        $this->_checkNotProcessed(self::WHERE_TYPE);
        $this->_parts[self::WHERE_TYPE] = $value;
        return $this;
    }

    public function ignoreVisible($value = true)
    {
        $this->_checkNotProcessed(self::IGNORE_VISIBLE);
        $this->_parts[self::IGNORE_VISIBLE] = $value;
        return $this;
    }

}
