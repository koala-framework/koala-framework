<?php
/**
 * FilterIterator that filters based on file extension
 *
 * Usage Example:
 * $it = new RecursiveDirectoryIterator('.');
 * $it = new RecursiveIteratorIterator($it);
 * $it = new Kwf_Iterator_Filter_FileExtension($it, array('js', 'css'));
 */
class Kwf_Iterator_Filter_FileExtension extends FilterIterator
{
    protected $it;
    private $_extensions;

    /**
     * @param $iterator
     * @param string/array Extension(s) that should be accepted
     */
    function __construct($iterator, $extensions)
    {
        $this->it = $iterator;
        if (!is_array($extensions)) $extensions = array($extensions);
        $this->_extensions = $extensions;
        parent::__construct($this->it);
    }

    function accept()
    {
        if (!$this->it->getSubIterator()->isFile()) return false;
        foreach ($this->_extensions as $ext) {
            if (preg_match('/^.+\.'.$ext.'$/i', $this->it->getSubIterator()->getFilename())) {
                return true;
            }
        }
        return false;
    }
}
