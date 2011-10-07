<?php
class Kwf_Model_Select_Expr_Area implements Kwf_Model_Select_Expr_Interface
{
    protected $_field;
    protected $_latitude;
    protected $_longitude;
    protected $_radius;

    public function __construct($latitude, $longitude, $radius) {
        $this->_latitude = $latitude;
        $this->_longitude = $longitude;
        $this->_radius = $radius;
    }

    public function getLatitude()
    {
        return $this->_latitude;
    }

    public function getLongitude()
    {
        return $this->_longitude;
    }

    public function getRadius()
    {
        return $this->_radius;
    }

    public function validate()
    {
        if (!$this->_latitude || !$this->_longitude || !$this->_radius) {
            throw new Kwf_Exception("latitude, longitude und radius have to be set for '"+get_class($this)+"'");
        }
    }

    public function getResultType()
    {
        return Kwf_Model_Interface::TYPE_BOOLEAN;
    }

    public function toArray()
    {
        return array(
            'exprType' => str_replace('Vps_Model_Select_Expr_', '', get_class($this)),
            'latitude' =>  $this->_latitude,
            'longitude' => $this->_longitude,
            'radius' =>  $this->_radius,
        );
    }

    public static function fromArray(array $data)
    {
        $cls = 'Vps_Model_Select_Expr_'.$data['exprType'];
        $expressions = array();
        foreach ($data['expressions'] as $i) {
            $expressions[] = Vps_Model_Select_Expr::fromArray($i);
        }
        return new $cls($data['latitude'], $data['longitude'], $data['radius']);
    }
}