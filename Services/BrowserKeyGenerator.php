<?php
namespace RC\OpenSSLCryptoBundle\Services;
use RC\OpenSSLCryptoBundle\Services\KeyGenerator;
use RC\OpenSSLCryptoBundle\Entity\CSR;

class BrowserKeyGenerator {

    protected $certfile;
    protected $conf_path;
    protected $private_path;
    protected $privatekey;
    protected $sscert;

    public function __construct($server_pass, $certfile, $private_path, $config){
        $this->passphrase = $server_pass;
        $this->conf_path = $config;
        $this->certfile = $certfile;
        $this->private_path = $private_path;
    }

    public function generate(){

        try{

            $config = array(
                "config" => $this->conf_path,
            );

            $privatekey = openssl_get_privatekey(file_get_contents($this->private_path), $this->passphrase);
            $res = openssl_get_privatekey($privatekey, $this->passphrase);

            $this->sscert = openssl_x509_read(file_get_contents($this->certfile));
            openssl_pkey_export($res, $this->privatekey, $this->passphrase, $config);

            return true;

        }catch(\Exception $e){
            var_dump($e->getMessage());
            return $e->getMessage();
        }



    }

    public function setCert(){

    }

    public function getClientCertificate($passphrase){

        $key = openssl_pkey_get_private($this->privatekey, $this->passphrase);

        openssl_pkcs12_export($this->sscert, $iis, $key,  $passphrase);

        return $iis;
    }




}