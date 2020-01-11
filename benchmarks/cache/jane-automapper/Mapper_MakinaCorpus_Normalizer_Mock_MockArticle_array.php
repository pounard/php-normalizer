<?php

final class Mapper_MakinaCorpus_Normalizer_Mock_MockArticle_array extends \Jane\AutoMapper\Mapper
{
    protected $hash = '1578745532';
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
        $context = $context->withIncrementedDepth();
        if ($context->isAllowedAttribute('id')) {
            $value_1 = null;
            if (null !== $value->getid()) {
                $value_1 =& $this->mappers['Mapper_Ramsey\\Uuid\\UuidInterface_array']->map($value->getid(), $context->withNewContext('id'));
            }
            $result['id'] = $value_1;
        }
        if ($context->isAllowedAttribute('createdAt')) {
            $result['createdAt'] = $value->getcreatedAt()->format('Y-m-d\\TH:i:sP');
        }
        if ($context->isAllowedAttribute('updatedAt')) {
            $value_2 = null;
            if (null !== $value->getupdatedAt()) {
                $value_2 = $value->getupdatedAt()->format('Y-m-d\\TH:i:sP');
            }
            $result['updatedAt'] = $value_2;
        }
        if ($context->isAllowedAttribute('authors')) {
            $values = array();
            foreach ($value->getauthors() as $value_3) {
                $values[] = $value_3;
            }
            $result['authors'] = $values;
        }
        if ($context->isAllowedAttribute('markup')) {
            $value_4 = null;
            if (null !== $value->getmarkup()) {
                $value_4 =& $this->mappers['Mapper_MakinaCorpus\\Normalizer\\Mock\\MockTextWithFormat_array']->map($value->getmarkup(), $context->withNewContext('markup'));
            }
            $result['markup'] = $value_4;
        }
        if ($context->isAllowedAttribute('title')) {
            $value_5 = null;
            if (null !== $value->gettitle()) {
                $value_5 = $value->gettitle();
            }
            $result['title'] = $value_5;
        }
        if ($context->isAllowedAttribute('foo')) {
            $value_6 = null;
            if (null !== $value->getfoo()) {
                $value_6 = $value->getfoo();
            }
            $result['foo'] = $value_6;
        }
        if ($context->isAllowedAttribute('bar')) {
            $result['bar'] = $value->getbar();
        }
        if ($context->isAllowedAttribute('baz')) {
            $result['baz'] = $value->getbaz();
        }
        if ($context->isAllowedAttribute('filename')) {
            $result['filename'] = $value->getfilename();
        }
        return $result;
    }
    public function injectMappers(\Jane\AutoMapper\AutoMapperInterface $autoMapper)
    {
        $this->mappers['Mapper_Ramsey\\Uuid\\UuidInterface_array'] = $autoMapper->getMapper('Ramsey\\Uuid\\UuidInterface', 'array');
        $this->mappers['Mapper_MakinaCorpus\\Normalizer\\Mock\\MockTextWithFormat_array'] = $autoMapper->getMapper('MakinaCorpus\\Normalizer\\Mock\\MockTextWithFormat', 'array');
    }
}
