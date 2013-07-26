<?php

namespace RC\OpenSSLCryptoBundle\Metadata\Driver;

use RC\OpenSSLCryptoBundle\Annotation\Decrypt;
use RC\OpenSSLCryptoBundle\Annotation\Encrypt;

use Doctrine\Common\Annotations\AnnotationReader,
    Doctrine\Common\Annotations\Reader,
    Doctrine\Common\Persistence\Mapping\Driver\AnnotationDriver as AbstractAnnotationDriver,
    Doctrine\Common\Persistence\Mapping\ClassMetadata,
    Doctrine\Common\Persistence\Mapping\Driver\MappingDriver,
    Doctrine\ODM\PHPCR\Event,
    Doctrine\ODM\PHPCR\Mapping\Annotations as ODM,
    Doctrine\Common\EventSubscriber,
    Doctrine\ODM\PHPCR\Mapping\MappingException;

class AnnotationDriver extends AbstractAnnotationDriver implements EventSubscriber
{
    protected $reader;
    protected $crypt;


    public function __construct($reader, $crypt){
        $this->reader = $reader;
        $this->crypt = $crypt;
    }

    public function loadMetadataForClass($className, ClassMetadata $metadata){

    }

    public function loadClassMetadata($event)
    {

    }

    public function postLoad($event)
    {
        $object = $event->getEntity();
        $class = new \ReflectionClass(get_class($object));
        $name = $class->name;

        foreach($class->getProperties() as $p){
            $propAnnotations = $this->reader->getPropertyAnnotations($p);

            foreach ($propAnnotations as $annot) {
                if ($annot instanceof Decrypt) {
                    $p->setAccessible(true);
                    $p->setValue($object, $this->crypt->decrypt(base64_decode($p->getValue($object))));
                }
            }

        }

    }

    public function prePersist($event)
    {
        $this->updateReferences($event);
    }

    public function preUpdate($event)
    {
        $this->updateReferences($event);
    }

    public function getSubscribedEvents()
    {
        return array(
            'postLoad',
            'prePersist',
            'preUpdate',
        );
    }


    protected function getNamespace()
    {
        return __NAMESPACE__;
    }

    private function updateReferences($event)
    {
        $class = new \ReflectionClass(get_class($event->getEntity()));
        $name = $class->name;

        foreach($class->getProperties() as $p){
            $propAnnotations = $this->reader->getPropertyAnnotations($p);

            foreach ($propAnnotations as $annot) {
                if ($annot instanceof Encrypt) {
                    $p->setAccessible(true);
                    //var_dump($this->crypt->encrypt($p->getValue($event->getEntity())));
                    $p->setValue($event->getEntity(), base64_encode($this->crypt->encrypt($p->getValue($event->getEntity()))));
                }
            }

        }

    }


}
