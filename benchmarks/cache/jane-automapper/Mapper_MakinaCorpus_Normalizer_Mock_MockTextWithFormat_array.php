<?php

final class Mapper_MakinaCorpus_Normalizer_Mock_MockTextWithFormat_array extends \Jane\AutoMapper\Mapper
{
    protected $hash = '1578675982';
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
        if ($context->isAllowedAttribute('text')) {
            $value_1 = null;
            if (null !== $value->gettext()) {
                $value_1 = $value->gettext();
            }
            $result['text'] = $value_1;
        }
        if ($context->isAllowedAttribute('format')) {
            $result['format'] = $value->getformat();
        }
        return $result;
    }
    public function injectMappers(\Jane\AutoMapper\AutoMapperInterface $autoMapper)
    {
    }
}
