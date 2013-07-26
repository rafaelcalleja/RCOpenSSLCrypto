<?php
namespace RC\OpenSSLCryptoBundle\Services;


class Crypter {

    protected $privatekey;
    protected $publickey;
    protected $crypttext;
    protected $private_path;
    protected $passphrase;


    public function __construct($private_path, $passphrase){
        $this->private_path = $private_path;
        $this->passphrase = $passphrase;
    }



    public function encrypt($plain){
        $privatekey = openssl_get_privatekey(file_get_contents($this->private_path), $this->passphrase);
        $res = openssl_get_privatekey($privatekey, $this->passphrase);
        openssl_private_encrypt($plain, $this->crypttext ,$res);

        return $this->crypttext;
    }

    public function decrypt($crypt){

        try{
            if(!isset($_SERVER['SSL_CLIENT_CERT']) || empty($_SERVER['SSL_CLIENT_CERT']) ) return $crypt;

            $publickey = openssl_get_publickey($_SERVER['SSL_CLIENT_CERT']);
            $k = openssl_get_publickey($publickey);
            openssl_public_decrypt($crypt, $plaintext, $publickey);

            return $plaintext;

        }catch(\Exception $e){
            return $crypt;
        }

    }


}

