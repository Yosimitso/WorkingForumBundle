<?php
namespace Yosimitso\WorkingForumBundle\Util;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Loader\AttributeLoader;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

abstract class ApiHelper
{
    public static function getSerializer(): Serializer
    {
        $encoders = [new JsonEncoder()];
        $defaultContext = [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function (object $object, string $format, array $context): null {
                return null;
            },
        ];
        $classMetadataFactory = new ClassMetadataFactory(new AttributeLoader());
        $normalizers = [new ObjectNormalizer(classMetadataFactory: $classMetadataFactory, defaultContext: $defaultContext)];

        return new Serializer($normalizers, $encoders);
    } 
}
