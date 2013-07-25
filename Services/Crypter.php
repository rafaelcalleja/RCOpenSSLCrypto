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

        // if(!$this->enable) return $plain;
        // Turn public key into resource
      /*s  $publickey = openssl_get_publickey(file_get_contents($this->public_path));

        //die(var_dump($this->public_path));
        // Encrypt
        openssl_seal($plain, $crypttext, $ekey, array($publickey));
        openssl_free_key($publickey);

        // Set values
        $this->crypttext = $crypttext;
        $this->ekey = $ekey[0];

        return $this->crypttext . '$://' . $this->ekey;*/

    }

    public function decrypt($crypt){

        if(!isset($_SERVER['SSL_CLIENT_CERT']) || empty($_SERVER['SSL_CLIENT_CERT']) ) return $crypt;
        $publickey = openssl_get_publickey($_SERVER['SSL_CLIENT_CERT']);
        $k = openssl_get_publickey($publickey);
        openssl_public_decrypt($crypt, $plaintext, $publickey);
        return $plaintext;
        //echo "String decrypt : $plaintext";

        /*if(!$this->enable) return $crypt;
        // Turn private key into resource
        $privatekey = openssl_get_privatekey(file_get_contents($this->private_path), $this->passphrase);

        // Decrypt
        openssl_open($crypt, $plaintext, $ekey, $privatekey);
        openssl_free_key($privatekey);

        // Return value
        return $plaintext;*/

    }


}

