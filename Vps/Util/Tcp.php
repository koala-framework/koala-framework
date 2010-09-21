<?php
class Vps_Util_Tcp
{
    /**
     * Get a free port number
     *
     * No perfect solution, race conditions can occur
     */
    public static function getFreePort($from, $host = 'localhost')
    {
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        $ret = $from;
        while (true) {
            if (@socket_bind($socket, $host, $ret)) {
                break;
            }
            $ret++;
            if ($ret > $from+100) {
                $this->fail('can\'t get free port number');
            }
        }
        socket_close($socket);
        return $ret;
    }
}
