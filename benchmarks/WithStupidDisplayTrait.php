<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Benchmarks;

trait WithStupidDisplayTrait
{
    private $debugEnabled = false;
    private $dieAtFirst = true;

    protected function display($object)
    {
        if ($this->debugEnabled) {
            \print_r($object);
            if ($this->dieAtFirst) {
                die();
            }
        }
    }
}