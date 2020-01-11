<?php

final class Mapper_Ramsey_Uuid_Converter_NumberConverterInterface_array extends \Jane\AutoMapper\Mapper
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
            $result = array();
        }
        return $result;
    }
    public function injectMappers(\Jane\AutoMapper\AutoMapperInterface $autoMapper)
    {
    }
}
