<?php
abstract class Kwf_Update
{
    protected $_tags = array();

    protected $_actions = array();
    protected $_legacyRevision;
    protected $_legacyName;
    protected $_uniqueName;

    protected $_progressBar = null;

    public function __construct($uniqueName)
    {
        if (preg_match('#(.*?)[0-9]+Legacy([0-9]+)#', $uniqueName, $m)) {
            $this->_legacyRevision = (int)$m[2];
            $this->_legacyName = $m[1].(int)$m[2];
        }
        $this->_uniqueName = $uniqueName;
        $this->_init();
    }

    public function getTags()
    {
        return $this->_tags;
    }

    public function setTags($tags)
    {
        $this->_tags = $tags;
        return $this;
    }

    public function appendTag($tag)
    {
        $this->_tags[] = $tag;
        return $this;
    }

    public function getLegacyRevision()
    {
        return $this->_legacyRevision;
    }

    public function getUniqueName()
    {
        return $this->_uniqueName;
    }

    public function getLegacyName()
    {
        return $this->_legacyName;
    }

    protected function _init()
    {
    }

    public function preUpdate()
    {
        $ret = array();
        foreach ($this->_actions as $a) {
            $res = $a->preUpdate();
            if ($res) {
                $ret[] = $res;
            }
        }
        return $ret;
    }

    public function postUpdate()
    {
        $ret = array();
        foreach ($this->_actions as $a) {
            $res = $a->postUpdate();
            if ($res) {
                $ret[] = $res;
            }
        }
        return $ret;
    }

    public function postClearCache()
    {
        $ret = array();
        foreach ($this->_actions as $a) {
            $res = $a->postClearCache();
            if ($res) {
                $ret[] = $res;
            }
        }
        return $ret;
    }

    public function postMaintenanceBootstrap()
    {
    }

    public function checkSettings()
    {
        $ret = array();
        foreach ($this->_actions as $a) {
            $res = $a->checkSettings();
            if ($res) {
                $ret[] = $res;
            }
        }
        return $ret;
    }

    public function update()
    {
        $ret = array();
        foreach ($this->_actions as $a) {
            $res = $a->update();
            if ($res) {
                $ret[] = $res;
            }
        }
        return $ret;
    }

    public function getProgressSteps()
    {
        return 1;
    }

    public function setProgressBar(Zend_ProgressBar $progressBar = null)
    {
        $this->_progressBar = $progressBar;
    }
}
