<?php
class Vps_Benchmark {
    
    static $instance = null;
    private $_processes = array();
    
    public static function getInstance() {
        if(self::$instance == null) {
            self::$instance = new Vps_Benchmark();
        }
        return self::$instance;
    }

    /**
     * Startet eine Sequenz
     * Wenn die Sequenz bereits gestartet wurde, passiert nichts.
     * <code>
     * $benchmark = Bleistift_Benchmark::getInstance();
     * $benchmark->startSequence("Test-Klasse wird geladen");
     * $testClass = new stdClass();
     * </code>
     *
     * @param string $identifier
     */
    public function startSequence($identifier)
    {
        if(!isset($this->_processes[$identifier])) {
            $this->_processes[$identifier] = array();
            $this->_processes[$identifier]['start'] = microtime(true);
        }
    }

    /**
     * Beendet eine Sequenz
     * <code>
     * $benchmark->stopSequence("Test-Klass wird geladen");
     * </code>
     *
     * @param string $identifier
     */
    public function stopSequence($identifier)
    {
        if(isset($this->_processes[$identifier])) {
            $this->_processes[$identifier]['stop'] = microtime(true);
            $this->_processes[$identifier]['duration'] = $this->_processes[$identifier]['stop'] - $this->_processes[$identifier]['start'];
        }
    }

    /**
     * Gibt ein Array mit den Resultaten zurück
     *
     * @return array
     */
    public function getResults() {
        return $this->_processes;
    }
} 