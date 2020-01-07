<?php

final class Mapper_array_MakinaCorpus_Normalizer_Mock_MockTextWithFormat extends \Jane\AutoMapper\Mapper
{
    protected $hash = '1578415845';
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
            if ($context->hasConstructorArgument('MakinaCorpus\\Normalizer\\Mock\\MockTextWithFormat', 'text')) {
                $constructArg = $context->getConstructorArgument('MakinaCorpus\\Normalizer\\Mock\\MockTextWithFormat', 'text');
            } else {
                $constructArg = NULL;
            }
            if ($context->hasConstructorArgument('MakinaCorpus\\Normalizer\\Mock\\MockTextWithFormat', 'format')) {
                $constructArg_1 = $context->getConstructorArgument('MakinaCorpus\\Normalizer\\Mock\\MockTextWithFormat', 'format');
            } else {
                $constructArg_1 = NULL;
            }
            $result = new \MakinaCorpus\Normalizer\Mock\MockTextWithFormat($constructArg, $constructArg_1);
        }
        return $result;
    }
    public function injectMappers(\Jane\AutoMapper\AutoMapperInterface $autoMapper)
    {
    }
}
