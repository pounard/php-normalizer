<?php

final class Mapper_array_Ramsey_Uuid_UuidInterface extends \Jane\AutoMapper\Mapper
{
    protected $hash = '1577156169';
    public function __construct()
    {
    }
    public function &map($value, \Jane\AutoMapper\Context $context)
    {
        if (null === $value) {
            return $value;
        }
        $result = $context->getObjectToPopulate();
        if (null === $result) {
            $result = new \Ramsey\Uuid\UuidInterface();
        }
        return $result;
    }
    public function injectMappers(\Jane\AutoMapper\AutoMapperInterface $autoMapper)
    {
    }
}
