<?php
interface Vps_Media_Output_IsValidInterface extends Vps_Media_Output_Interface
{
    const VALID = 'valid';
    const VALID_DONT_CACHE = 'validDontCache';
    const INVALID = false;
    public static function isValidMediaOutput($id, $type, $className);
}
