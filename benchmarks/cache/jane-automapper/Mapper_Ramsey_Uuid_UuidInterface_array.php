<?php

final class Mapper_Ramsey_Uuid_UuidInterface_array extends \Jane\AutoMapper\Mapper
{
    protected $hash = '1577156169';
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
        if ($context->isAllowedAttribute('bytes')) {
            $result['bytes'] = $value->getbytes();
        }
        if ($context->isAllowedAttribute('numberConverter')) {
            $result['numberConverter'] =& $this->mappers['Mapper_Ramsey\\Uuid\\Converter\\NumberConverterInterface_array']->map($value->getnumberConverter(), $context->withNewContext('numberConverter'));
        }
        if ($context->isAllowedAttribute('hex')) {
            $result['hex'] = $value->gethex();
        }
        if ($context->isAllowedAttribute('fieldsHex')) {
            $values = array();
            foreach ($value->getfieldsHex() as $value_1) {
                $values[] = $value_1;
            }
            $result['fieldsHex'] = $values;
        }
        if ($context->isAllowedAttribute('clockSeqHiAndReservedHex')) {
            $result['clockSeqHiAndReservedHex'] = $value->getclockSeqHiAndReservedHex();
        }
        if ($context->isAllowedAttribute('clockSeqLowHex')) {
            $result['clockSeqLowHex'] = $value->getclockSeqLowHex();
        }
        if ($context->isAllowedAttribute('clockSequenceHex')) {
            $result['clockSequenceHex'] = $value->getclockSequenceHex();
        }
        if ($context->isAllowedAttribute('dateTime')) {
            $result['dateTime'] = $value->getdateTime()->format('Y-m-d\\TH:i:sP');
        }
        if ($context->isAllowedAttribute('leastSignificantBitsHex')) {
            $result['leastSignificantBitsHex'] = $value->getleastSignificantBitsHex();
        }
        if ($context->isAllowedAttribute('mostSignificantBitsHex')) {
            $result['mostSignificantBitsHex'] = $value->getmostSignificantBitsHex();
        }
        if ($context->isAllowedAttribute('nodeHex')) {
            $result['nodeHex'] = $value->getnodeHex();
        }
        if ($context->isAllowedAttribute('timeHiAndVersionHex')) {
            $result['timeHiAndVersionHex'] = $value->gettimeHiAndVersionHex();
        }
        if ($context->isAllowedAttribute('timeLowHex')) {
            $result['timeLowHex'] = $value->gettimeLowHex();
        }
        if ($context->isAllowedAttribute('timeMidHex')) {
            $result['timeMidHex'] = $value->gettimeMidHex();
        }
        if ($context->isAllowedAttribute('timestampHex')) {
            $result['timestampHex'] = $value->gettimestampHex();
        }
        if ($context->isAllowedAttribute('urn')) {
            $result['urn'] = $value->geturn();
        }
        if ($context->isAllowedAttribute('variant')) {
            $result['variant'] = $value->getvariant();
        }
        if ($context->isAllowedAttribute('version')) {
            $value_2 = null;
            if (null !== $value->getversion()) {
                $value_2 = $value->getversion();
            }
            $result['version'] = $value_2;
        }
        return $result;
    }
    public function injectMappers(\Jane\AutoMapper\AutoMapperInterface $autoMapper)
    {
        $this->mappers['Mapper_Ramsey\\Uuid\\Converter\\NumberConverterInterface_array'] = $autoMapper->getMapper('Ramsey\\Uuid\\Converter\\NumberConverterInterface', 'array');
    }
}
