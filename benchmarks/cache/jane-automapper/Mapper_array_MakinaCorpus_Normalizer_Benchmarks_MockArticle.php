<?php

final class Mapper_array_MakinaCorpus_Normalizer_Benchmarks_MockArticle extends \Jane\AutoMapper\Mapper
{
    protected $hash = '1577989139';
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
            $result = new \MakinaCorpus\Normalizer\Benchmarks\MockArticle();
        }
        if (array_key_exists('updateddAt', $value) && $context->isAllowedAttribute('updateddAt')) {
            $value_1 = null;
            if (null !== $value['updateddAt']) {
                $value_1 = \DateTimeImmutable::createFromFormat('Y-m-d\\TH:i:sP', $value['updateddAt']);
            }
            $result->setupdateddAt($value_1);
        }
        if (array_key_exists('authors', $value) && $context->isAllowedAttribute('authors')) {
            $values = array();
            foreach ($value['authors'] as $value_2) {
                $values[] = $value_2;
            }
            $result->setauthors($values);
        }
        if (array_key_exists('title', $value) && $context->isAllowedAttribute('title')) {
            $value_3 = null;
            if (null !== $value['title']) {
                $value_3 = $value['title'];
            }
            $result->settitle($value_3);
        }
        if (array_key_exists('foo', $value) && $context->isAllowedAttribute('foo')) {
            $value_4 = null;
            if (null !== $value['foo']) {
                $value_4 = $value['foo'];
            }
            $result->setfoo($value_4);
        }
        if (array_key_exists('bar', $value) && $context->isAllowedAttribute('bar')) {
            $result->setbar($value['bar']);
        }
        if (array_key_exists('baz', $value) && $context->isAllowedAttribute('baz')) {
            $result->setbaz($value['baz']);
        }
        if (array_key_exists('filename', $value) && $context->isAllowedAttribute('filename')) {
            $result->setfilename($value['filename']);
        }
        return $result;
    }
    public function injectMappers(\Jane\AutoMapper\AutoMapperInterface $autoMapper)
    {
    }
}
