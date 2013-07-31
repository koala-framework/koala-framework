<?php
/**
 * execute everytime before render to check if it should use view-cache or render again
 *
 */
interface Kwf_Component_Plugin_Interface_UseViewCache
{
    public function useViewCache($renderer);
}
