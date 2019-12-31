<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Tests\Unit\Mock;

final class MockHelper
{
    /**
     * Arbitrary change object properties, and return the same object reference.
     */
    final public static function changeObjectProperties($object, array $values)
    {
        $class = \get_class($object);
        do {
            $ref = new \ReflectionClass($class);
            $fun = static function () use ($object, $values, $ref) {
                foreach ($values as $key => $value) {
                    if ($ref->hasProperty($key)) {
                        $object->{$key} = $value;
                    }
                }
            };
            (\Closure::bind($fun, null, $class))();
        } while ($class = \get_parent_class($class));
        return $object;
    }
}
