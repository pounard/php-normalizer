<?php

final class Mapper_array_MakinaCorpus_Normalizer_Mock_MockArticle extends \Jane\AutoMapper\Mapper
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
            $result = new \MakinaCorpus\Normalizer\Mock\MockArticle();
        }
        $context = $context->withIncrementedDepth();
        if (array_key_exists('id', $value) && $context->isAllowedAttribute('id')) {
            $value_1 = null;
            if (null !== $value['id']) {
                $value_1 =& $this->mappers['Mapper_array_Ramsey\\Uuid\\UuidInterface']->map($value['id'], $context->withNewContext('id'));
            }
            $result->setid($value_1);
        }
        if (array_key_exists('createdAt', $value) && $context->isAllowedAttribute('createdAt')) {
            $result->setcreatedAt(\DateTimeImmutable::createFromFormat('Y-m-d\\TH:i:sP', $value['createdAt']));
        }
        if (array_key_exists('updatedAt', $value) && $context->isAllowedAttribute('updatedAt')) {
            $value_2 = null;
            if (null !== $value['updatedAt']) {
                $value_2 = \DateTimeImmutable::createFromFormat('Y-m-d\\TH:i:sP', $value['updatedAt']);
            }
            $result->setupdatedAt($value_2);
        }
        if (array_key_exists('authors', $value) && $context->isAllowedAttribute('authors')) {
            $values = array();
            foreach ($value['authors'] as $value_3) {
                $values[] = $value_3;
            }
            $result->setauthors($values);
        }
        if (array_key_exists('markup', $value) && $context->isAllowedAttribute('markup')) {
            $value_4 = null;
            if (null !== $value['markup']) {
                $value_4 =& $this->mappers['Mapper_array_MakinaCorpus\\Normalizer\\Mock\\MockTextWithFormat']->map($value['markup'], $context->withNewContext('markup'));
            }
            $result->setmarkup($value_4);
        }
        if (array_key_exists('title', $value) && $context->isAllowedAttribute('title')) {
            $value_5 = null;
            if (null !== $value['title']) {
                $value_5 = $value['title'];
            }
            $result->settitle($value_5);
        }
        if (array_key_exists('foo', $value) && $context->isAllowedAttribute('foo')) {
            $value_6 = null;
            if (null !== $value['foo']) {
                $value_6 = $value['foo'];
            }
            $result->setfoo($value_6);
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
        $this->mappers['Mapper_array_Ramsey\\Uuid\\UuidInterface'] = $autoMapper->getMapper('array', 'Ramsey\\Uuid\\UuidInterface');
        $this->mappers['Mapper_array_MakinaCorpus\\Normalizer\\Mock\\MockTextWithFormat'] = $autoMapper->getMapper('array', 'MakinaCorpus\\Normalizer\\Mock\\MockTextWithFormat');
    }
}
