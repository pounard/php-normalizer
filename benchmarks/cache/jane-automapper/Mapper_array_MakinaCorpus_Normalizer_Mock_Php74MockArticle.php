<?php

final class Mapper_array_MakinaCorpus_Normalizer_Mock_Php74MockArticle extends \Jane\AutoMapper\Mapper
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
            $result = new \MakinaCorpus\Normalizer\Mock\Php74MockArticle();
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
            $value_2 = null;
            if (null !== $value['createdAt']) {
                $value_2 = \DateTimeImmutable::createFromFormat('Y-m-d\\TH:i:sP', $value['createdAt']);
            }
            $result->setcreatedAt($value_2);
        }
        if (array_key_exists('updatedAt', $value) && $context->isAllowedAttribute('updatedAt')) {
            $value_3 = null;
            if (null !== $value['updatedAt']) {
                $value_3 = \DateTimeImmutable::createFromFormat('Y-m-d\\TH:i:sP', $value['updatedAt']);
            }
            $result->setupdatedAt($value_3);
        }
        if (array_key_exists('authors', $value) && $context->isAllowedAttribute('authors')) {
            $values = array();
            foreach ($value['authors'] as $value_4) {
                $values[] = $value_4;
            }
            $result->setauthors($values);
        }
        if (array_key_exists('markup', $value) && $context->isAllowedAttribute('markup')) {
            $value_5 = null;
            if (null !== $value['markup']) {
                $value_5 =& $this->mappers['Mapper_array_MakinaCorpus\\Normalizer\\Mock\\Php74MockTextWithFormat']->map($value['markup'], $context->withNewContext('markup'));
            }
            $result->setmarkup($value_5);
        }
        if (array_key_exists('title', $value) && $context->isAllowedAttribute('title')) {
            $value_6 = null;
            if (null !== $value['title']) {
                $value_6 = $value['title'];
            }
            $result->settitle($value_6);
        }
        if (array_key_exists('foo', $value) && $context->isAllowedAttribute('foo')) {
            $value_7 = null;
            if (null !== $value['foo']) {
                $value_7 = $value['foo'];
            }
            $result->setfoo($value_7);
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
        $this->mappers['Mapper_array_MakinaCorpus\\Normalizer\\Mock\\Php74MockTextWithFormat'] = $autoMapper->getMapper('array', 'MakinaCorpus\\Normalizer\\Mock\\Php74MockTextWithFormat');
    }
}
