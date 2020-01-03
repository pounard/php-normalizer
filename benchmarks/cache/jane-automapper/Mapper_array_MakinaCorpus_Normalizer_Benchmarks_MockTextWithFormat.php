<?php

final class Mapper_array_MakinaCorpus_Normalizer_Benchmarks_MockTextWithFormat extends \Jane\AutoMapper\Mapper
{
    protected $hash = '1578041530';
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
            if ($context->hasConstructorArgument('MakinaCorpus\\Normalizer\\Benchmarks\\MockTextWithFormat', 'text')) {
                $constructArg = $context->getConstructorArgument('MakinaCorpus\\Normalizer\\Benchmarks\\MockTextWithFormat', 'text');
            } else {
                $constructArg = NULL;
            }
            if ($context->hasConstructorArgument('MakinaCorpus\\Normalizer\\Benchmarks\\MockTextWithFormat', 'format')) {
                $constructArg_1 = $context->getConstructorArgument('MakinaCorpus\\Normalizer\\Benchmarks\\MockTextWithFormat', 'format');
            } else {
                $constructArg_1 = NULL;
            }
            $result = new \MakinaCorpus\Normalizer\Benchmarks\MockTextWithFormat($constructArg, $constructArg_1);
        }
        return $result;
    }
    public function injectMappers(\Jane\AutoMapper\AutoMapperInterface $autoMapper)
    {
    }
}
