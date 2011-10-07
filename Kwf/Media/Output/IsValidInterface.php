<?php
interface Vps_Media_Output_IsValidInterface extends Vps_Media_Output_Interface
{
    const VALID = 'valid';
    const VALID_DONT_CACHE = 'validDontCache';
    const INVALID = false;
    const ACCESS_DENIED = 'accessDenied';
    public static function isValidMediaOutput($id, $type, $className);
}
