<?php

final class Mapper_array_MakinaCorpus_Normalizer_Mock_MockArticle extends \Jane\AutoMapper\Mapper
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
            $result = new \MakinaCorpus\Normalizer\Mock\MockArticle();
        }
        $context = $context->withIncrementedDepth();
        if (array_key_exists('updatedAt', $value) && $context->isAllowedAttribute('updatedAt')) {
            $value_1 = null;
            if (null !== $value['updatedAt']) {
                $value_1 = \DateTimeImmutable::createFromFormat('Y-m-d\\TH:i:sP', $value['updatedAt']);
            }
            $result->setupdatedAt($value_1);
        }
        if (array_key_exists('authors', $value) && $context->isAllowedAttribute('authors')) {
            $values = array();
            foreach ($value['authors'] as $value_2) {
                $values[] = $value_2;
            }
            $result->setauthors($values);
        }
        if (array_key_exists('text', $value) && $context->isAllowedAttribute('text')) {
            $value_3 = null;
            if (null !== $value['text']) {
                $value_3 =& $this->mappers['Mapper_array_MakinaCorpus\\Normalizer\\Mock\\MockTextWithFormat']->map($value['text'], $context->withNewContext('text'));
            }
            $result->settext($value_3);
        }
        if (array_key_exists('title', $value) && $context->isAllowedAttribute('title')) {
            $value_4 = null;
            if (null !== $value['title']) {
                $value_4 = $value['title'];
            }
            $result->settitle($value_4);
        }
        if (array_key_exists('foo', $value) && $context->isAllowedAttribute('foo')) {
            $value_5 = null;
            if (null !== $value['foo']) {
                $value_5 = $value['foo'];
            }
            $result->setfoo($value_5);
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
        $this->mappers['Mapper_array_MakinaCorpus\\Normalizer\\Mock\\MockTextWithFormat'] = $autoMapper->getMapper('array', 'MakinaCorpus\\Normalizer\\Mock\\MockTextWithFormat');
    }
}
