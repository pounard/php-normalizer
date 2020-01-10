<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer;

/**
 * Option type for RuntimeHelper::find() return.
 */
final class ValueOption
{
    /** @var bool */
    public $success = false;

    /** @var mixed */
    public $value;

    /**
     * Found a value.
     */
    public static function ok($value): self
    {
        $ret = new self;
        $ret->value = $value;
        $ret->success = true;
        return $ret;
    }

    /**
     * Could not find value.
     */
    public static function miss(): self
    {
        return new self;
    }
}
