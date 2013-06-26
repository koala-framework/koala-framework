<?php
/**
 * File based high performance counter
 *
 * Can be used when Kwf_Benchmark_Counter (which used apc) won't work because a script
 * is run on cli.
 *
 * Optionally buffers writes to the file - which is useful for long-running scripts or
 * for situations where lots of increments in the same script are expected.
 */
class Kwf_Benchmark_BufferedFileCounter
{
    private $_file;
    private $_bufferTime = false;

    private $_increments = array();
    private $_updates = array();
    private $_lastWrite;
    private $_readValuesCache = null;

    public function __construct(array $config)
    {
        $this->_file = $config['file'];
        if (isset($config['bufferTime'])) $this->_bufferTime = $config['bufferTime'];
        $this->_lastWrite = time();
    }

    public function __destruct()
    {
        $this->flush();
    }

    public function increment($var, $value = 1)
    {
        if (!isset($this->_increments[$var])) {
            $this->_increments[$var] = 0;
        }
        $this->_increments[$var] += $value;
        if (!$this->_bufferTime || (time() - $this->_lastWrite) > $this->_bufferTime) {
            $this->flush();
        }
        $this->_readValuesCache = null;
    }

    public function update($var, $value)
    {
        $this->_updates[$var] = $value;
        if (!$this->_bufferTime || (time() - $this->_lastWrite) > $this->_bufferTime) {
            $this->flush();
        }
        $this->_readValuesCache = null;
    }

    public function flush()
    {
        $this->_lastWrite = time();

        if (!$this->_increments && !$this->_updates) return;

        $fp = fopen($this->_file, "c+");
        if (flock($fp, LOCK_EX)) {
            $contents = stream_get_contents($fp);
            if ($contents) {
                $contents = (array)json_decode($contents, true);
            } else {
                $contents = array();
            }
            foreach ($this->_updates as $k=>$i) {
                $contents[$k] = $i;
            }
            foreach ($this->_increments as $k=>$i) {
                if (!isset($contents[$k])) $contents[$k] = 0;
                $contents[$k] += $i;
            }
            ftruncate($fp, 0);
            fseek($fp, SEEK_SET, 0);
            fwrite($fp, json_encode($contents));
            flock($fp, LOCK_UN);
            $this->_increments = array();
        }
        fclose($fp);
    }

    public function getValues()
    {
        if (!file_exists($this->_file)) return array();
        $fp = fopen($this->_file, "r");
        if (flock($fp, LOCK_SH)) {
            $writeFeedCounters = stream_get_contents($fp);
            if ($writeFeedCounters) {
                $writeFeedCounters = json_decode($writeFeedCounters);
            } else {
                $writeFeedCounters = array();
            }
            flock($fp, LOCK_UN);
        }
        fclose($fp);
        return (array)$writeFeedCounters;
    }

    public function getValue($var)
    {
        if (!isset($this->_readValuesCache)) {
            $this->_readValuesCache = $this->getValues();
        }
        if (isset($this->_readValuesCache[$var])) {
            return $this->_readValuesCache[$var];
        }
        return 0;
    }
}
