<?php
/**
 * Wird benötigt um Exceptions zu serialisieren und wirft den trace weg
 * (PDOException ist tlw. im trace und die lässt sich nicht serialisieren).
 * Wird z.B. benötigt im Service.
 */
class Vps_Exception_Serializable extends Vps_Exception implements Serializable
{
    private $_exception;

    public function __construct(Exception $exception)
    {
        $this->_exception = $exception;
    }

    public function getException()
    {
        return $this->_exception;
    }

    public function serialize()
    {
        return serialize(array(
            get_class($this->_exception),
            $this->_exception->message,
            $this->_exception->code,
            $this->_exception->file,
            $this->_exception->line
        ));
    }

    public function unserialize($serialized)
    {
        $data = unserialize($serialized);
        $this->_exception = new $data[0];
        $this->_exception->message = $data[1];
        $this->_exception->code = $data[2];
        $this->_exception->file = $data[3];
        $this->_exception->line = $data[4];
    }
}
