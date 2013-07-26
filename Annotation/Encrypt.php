<?php

namespace RC\OpenSSLCryptoBundle\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 */
class Encrypt
{

    public $prePersist = true;
    public $preUpdate = true;

    public function __get($name)
    {
       // die(var_dump($values));
    }

    public function __set($name, $values)
    {
       // die(var_dump(__FUNCTION__,$values));
    }
}
