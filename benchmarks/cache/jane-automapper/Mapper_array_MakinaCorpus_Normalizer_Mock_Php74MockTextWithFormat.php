<?php

final class Mapper_array_MakinaCorpus_Normalizer_Mock_Php74MockTextWithFormat extends \Jane\AutoMapper\Mapper
{
    protected $hash = '1578733756';
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
            if ($context->hasConstructorArgument('MakinaCorpus\\Normalizer\\Mock\\Php74MockTextWithFormat', 'text')) {
                $constructArg = $context->getConstructorArgument('MakinaCorpus\\Normalizer\\Mock\\Php74MockTextWithFormat', 'text');
            } else {
                $value_1 = null;
                if (null !== $value['text']) {
                    $value_1 = $value['text'];
                }
                $constructArg = $value_1;
            }
            if ($context->hasConstructorArgument('MakinaCorpus\\Normalizer\\Mock\\Php74MockTextWithFormat', 'format')) {
                $constructArg_1 = $context->getConstructorArgument('MakinaCorpus\\Normalizer\\Mock\\Php74MockTextWithFormat', 'format');
            } else {
                $value_2 = null;
                if (null !== $value['format']) {
                    $value_2 = $value['format'];
                }
                $constructArg_1 = $value_2;
            }
            $result = new \MakinaCorpus\Normalizer\Mock\Php74MockTextWithFormat($constructArg, $constructArg_1);
        }
        return $result;
    }
    public function injectMappers(\Jane\AutoMapper\AutoMapperInterface $autoMapper)
    {
    }
}
