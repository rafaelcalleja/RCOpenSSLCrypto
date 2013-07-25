<?php
namespace RC\OpenSSLCryptoBundle\Entity;

class CSR {

    protected $countryName;
    protected $stateOrProvinceName;
    protected $localityName;
    protected $organizationName;
    protected $organizationalUnitName;
    protected $commonName;
    protected $emailAddress;

    /**
     * @param mixed $commonName
     */
    public function setCommonName($commonName)
    {
        $this->commonName = $commonName;
    }

    /**
     * @return mixed
     */
    public function getCommonName()
    {
        return $this->commonName;
    }

    /**
     * @param mixed $countryName
     */
    public function setCountryName($countryName)
    {
        $this->countryName = $countryName;
    }

    /**
     * @return mixed
     */
    public function getCountryName()
    {
        return $this->countryName;
    }

    /**
     * @param mixed $emailAddress
     */
    public function setEmailAddress($emailAddress)
    {
        $this->emailAddress = $emailAddress;
    }

    /**
     * @return mixed
     */
    public function getEmailAddress()
    {
        return $this->emailAddress;
    }

    /**
     * @param mixed $localityName
     */
    public function setLocalityName($localityName)
    {
        $this->localityName = $localityName;
    }

    /**
     * @return mixed
     */
    public function getLocalityName()
    {
        return $this->localityName;
    }

    /**
     * @param mixed $organizationName
     */
    public function setOrganizationName($organizationName)
    {
        $this->organizationName = $organizationName;
    }

    /**
     * @return mixed
     */
    public function getOrganizationName()
    {
        return $this->organizationName;
    }

    /**
     * @param mixed $organizationalUnitName
     */
    public function setOrganizationalUnitName($organizationalUnitName)
    {
        $this->organizationalUnitName = $organizationalUnitName;
    }

    /**
     * @return mixed
     */
    public function getOrganizationalUnitName()
    {
        return $this->organizationalUnitName;
    }

    /**
     * @param mixed $stateOrProvinceName
     */
    public function setStateOrProvinceName($stateOrProvinceName)
    {
        $this->stateOrProvinceName = $stateOrProvinceName;
    }

    /**
     * @return mixed
     */
    public function getStateOrProvinceName()
    {
        return $this->stateOrProvinceName;
    }

    public function toArray(){
        return array_map( function($data){ return $data ; }, get_object_vars($this) );
    }

}

