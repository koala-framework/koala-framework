<?php
/**
 * Memcache & MySQL PHP Session Handler
 * 
 * original author Jakub Matějka <jakub@keboola.com>
 * @see http://pureform.wordpress.com/2009/04/08/memcache-mysql-php-session-handler/
 */
class Kwf_Util_SessionHandler
{
    /**
     * @var int
     */
    protected $_lifeTime;

    /**
     * @var Memcache
     */
    private $_memcache;
 
    /**
     * @var string
     */
    private $_initSessionData;
 
    /**
     * interval for session expiration update in the DB
     * @var int
     */
    protected $_refreshTime;

    public static function init()
    {
        $h = new self();
        session_set_save_handler(
            array($h, 'open'),
            array($h, 'close'),
            array($h, 'read'),
            array($h, 'write'),
            array($h, 'destroy'),
            array($h, 'gc')
        );

        // this ensures to write down and close the session when destroying the handler object
        register_shutdown_function("session_write_close");
    }
 
    /**
     * opening of the session - mandatory arguments won't be needed
     *
     * @param string $savePath
     * @param string $sessionName
     * @return bool
     */
    public function open($savePath, $sessionName)
    {
        if (!isset($this->_lifeTime)) $this->_lifeTime = intval(ini_get("session.gc_maxlifetime"));
        if (!isset($this->_refreshTime)) $this->_refreshTime = ceil($this->_lifeTime / 3);

        $this->_initSessionData = null;

        $this->_memcache = new Memcache();
        if (Kwf_Config::getValue('aws.simpleCacheCluster')) {
            $servers = Kwf_Util_Aws_ElastiCache_CacheClusterEndpoints::getCached(Kwf_Config::getValue('aws.simpleCacheCluster'));
        } else if (Kwf_Cache_Simple::$memcacheHost) {
            $servers = array(
                array(
                    'host' => Kwf_Cache_Simple::$memcacheHost,
                    'port' => Kwf_Cache_Simple::$memcachePort
                )
            );
        } else {
            throw new Kwf_Exception("no memcache configured");
        }
        foreach ($servers as $s) {
            if (version_compare(phpversion('memcache'), '2.1.0') == -1 || phpversion('memcache')=='2.2.4') { // < 2.1.0
                $this->_memcache->addServer($s['host'], $s['port'], true, 1, 1, 1);
            } else if (version_compare(phpversion('memcache'), '3.0.0') == -1) { // < 3.0.0
                $this->_memcache->addServer($s['host'], $s['port'], true, 1, 1, 1, true, null, 10000);
            } else {
                $this->_memcache->addServer($s['host'], $s['port'], true, 1, 1, 1);
            }
        }

        return true;
    }
 
    /**
     * closing the session
     *
     * @return bool
     */
    public function close()
    {
        $this->_initSessionData = null;
        return true;
    }
 
    /**
     * reading of the session data
     * if the data couldn't be found in the Memcache, we try to load it from the DB
     * we have to update the time of data expiration in the db using _updateDbExpiration()
     * the life time in Memcache is updated automatically by write operation
     *
     * @param string $sessionId
     * @return string
     */
    function read($sessionId)
    {
        $this->_initSessionData = '';
        $d = $this->_memcache->get(Kwf_Cache_Simple::getUniquePrefix().'sess-'.$sessionId);
        if ($d === false) {
            Kwf_Benchmark::count('sessionhdl', 'load from db');
            //the record could not be found in the Memcache, loading from the db
            $this->_initSessionData = Kwf_Registry::get('db')->query("SELECT data FROM kwf_sessions WHERE sessionId=?", $sessionId)->fetchColumn();
            if ($this->_initSessionData) {
                //record found in the db, cache in memcache
                self::_updateDbExpiration($sessionId);
            } else {
                //record not in the db
                $this->_initSessionData = '';
            }
        } else {
            $expiration = $d['expiration'];
            if ($expiration+$this->_refreshTime < time()) {
                //expired; nothing to do
            } else {
                $this->_initSessionData = $d['data'];
                //if we didn't write into the db for at least
                //$this->_refreshTime (5 minutes), we need to refresh the expiration time in the db
                $ttl = $expiration - time();
                if ($ttl < $this->_refreshTime) {
                    self::_updateDbExpiration($sessionId);
                }
            }
        }
        return $this->_initSessionData;
    }
 
    /**
     * update of the expiration time of the db record
     *
     * @param string $sessionId
     */
    private function _updateDbExpiration($sessionId)
    {
        Kwf_Benchmark::count('sessionhdl', '_updateDbExpiration');
        $expiration = $this->_lifeTime + time();
        Kwf_Registry::get('db')->query("UPDATE kwf_sessions SET expiration=? WHERE sessionId=?", array($expiration, $sessionId));

        //we store the time of the new expiration into the Memcache
        $this->_memcacheSet($sessionId, $this->_initSessionData);
    }

    private function _memcacheSet($sessionId, $data)
    {
        $d = array(
            'data' => $data,
            'expiration' => $this->_lifeTime + time()
        );
        $this->_memcache->set(
            Kwf_Cache_Simple::getUniquePrefix().'sess-'.$sessionId,
            $d,
            false,
            time() + $this->_lifeTime + $this->_refreshTime
        );
    }
 
    /**
     * cache write - this is called when the script is about to finish, or when session_write_close() is called
     * data are written only when something has changed
     *
     * @param string $sessionId
     * @param string $data
     * @return bool
     */
    public function write($sessionId, $data)
    {
        $t = microtime(true);
        $expiration = $this->_lifeTime + time();
 
        //we store time of the db record expiration in the Memcache
        if ($this->_initSessionData !== $data) {
            Kwf_Registry::get('db')->query("REPLACE INTO kwf_sessions (sessionId, expiration, data) VALUES(?, ?, ?)", array($sessionId, $expiration, $data));
            $this->_memcacheSet($sessionId, $data);
        }
        Kwf_Benchmark::count('sessionhdl', 'read: '.((microtime(true)-$t)*1000).'ms');
        return true;
    }
 
    /**
     * destroy of the session
     *
     * @param string $sessionId
     * @return bool
     */
    public function destroy($sessionId)
    {
        $this->_memcache->delete(Kwf_Cache_Simple::getUniquePrefix().'sess-'.$sessionId);
        $this->_memcache->delete(Kwf_Cache_Simple::getUniquePrefix().'sess-db-'.$sessionId);
        Kwf_Registry::get('db')->query("DELETE FROM kwf_sessions WHERE sessionId=?", $sessionId); 
        return true;
    }
 
    /**
     * called by the garbage collector
     *
     * @param int $maxlifetime
     * @return bool
     */
    public function gc($maxlifetime)
    {
        foreach (Kwf_Registry::get('db')->query("SELECT sessionId FROM kwf_sessions WHERE expiration < ?", time())->fetchAll() as $r) {
            $this->destroy($r['sessionId']);
        }
        return true;
    }
}
