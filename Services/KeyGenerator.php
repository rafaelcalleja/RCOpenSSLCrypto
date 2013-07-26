<?php
namespace RC\OpenSSLCryptoBundle\Services;

use RC\OpenSSLCryptoBundle\Entity\CSR;

class KeyGenerator {

    protected $entity;
    protected $csr;
    protected $conf_path;
    protected $days_valid;
    protected $passphrase;
    protected $publickey;
    protected $privatekey;
    protected $sscert;

    public function __construct(CSR $entity, $conf_path, $days_valid, $passphrase){
        $this->entity = $entity;
        $this->conf_path = $conf_path;
        $this->days_valid = $days_valid;
        $this->passphrase = $passphrase;
    }

    public function generate(){

        try{

            $dn = $this->entity->toArray();

            $config = array(
                "config" => $this->conf_path,
            );

            $privkey = openssl_pkey_new();


            $csr = openssl_csr_new($dn, $privkey, $config);

            $this->sscert = openssl_csr_sign($csr, null, $privkey, $this->days_valid, $config);

            openssl_x509_export($this->sscert, $this->publickey);

            openssl_pkey_export($privkey, $this->privatekey, $this->passphrase, $config);

            openssl_csr_export($csr, $this->csr);

            return true;

        }catch(\Exception $e){
            return $e->getMessage();
        }



    }

    public function getClientCertificate($passphrase){

        $key = openssl_pkey_get_private($this->privatekey, $this->passphrase);

        openssl_pkcs12_export($this->sscert, $iis, $key,  $passphrase);

        return $iis;
    }


    public function getCert(){
        return $this->sscert;
    }

    /**
     * @return mixed
     */
    public function getPrivatekey()
    {
        return $this->privatekey;
    }

    /**
     * @return mixed
     */
    public function getPublickey()
    {
        return $this->publickey;
    }

    /**
     * @return mixed
     */
    public function getCsr()
    {
        return $this->csr;
    }




}