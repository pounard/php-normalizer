<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Benchmarks\Unrelated;

/**
 * @Warmup(1)
 * @Revs(5)
 * @Iterations(5)
 */
final class InstanceOfBench
{
    private $object;
    private $class = 'DateTime';

    public function __construct()
    {
        $this->object = new \DateTime();
    }

    public function benchInstanceOfVariable()
    {
        if ($this->object instanceof $this->class) {
            time();
        }
    }

    public function benchInstanceOf()
    {
        if ($this->object instanceof \DateTime) {
            time();
        }
    }

    public function benchGetClass()
    {
        if (\get_class($this->object) === 'DateTime') {
            time();
        }
    }
}
