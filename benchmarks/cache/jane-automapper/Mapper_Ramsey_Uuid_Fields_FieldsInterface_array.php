<?php

final class Mapper_Ramsey_Uuid_Fields_FieldsInterface_array extends \Jane\AutoMapper\Mapper
{
    protected $hash = '1578744718';
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
        if ($context->isAllowedAttribute('bytes')) {
            $result['bytes'] = $value->getbytes();
        }
        return $result;
    }
    public function injectMappers(\Jane\AutoMapper\AutoMapperInterface $autoMapper)
    {
    }
}
