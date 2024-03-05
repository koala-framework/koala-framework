<?php
class Kwf_Util_Symfony
{
    private static $_instance;

    /**
     * Don't use this method in Symfony context
     *
     * @return Kwf_SymfonyKernel
     */
    public static function getKernel()
    {
        if (!isset(self::$_instance)) {
            if ($cls = Kwf_Config::getValue('symfony.kernelClass')) {
                if (!class_exists('Symfony\Component\HttpKernel\Kernel')) return null;

                self::$_instance = new $cls();
                self::$_instance->boot(); //make sure it is booted (won't do it twice)
            } else {
                return null;
            }
        }
        return self::$_instance;
    }
}
