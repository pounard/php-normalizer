<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Generator;

/**
 * File writer.
 */
final class Writer
{
    const INDENT_SIZE = 4;

    /** @var resource */
    private $handle;

    /**
     * Constructor
     */
    public function __construct(string $filename)
    {
        if (\file_exists($filename)) {
            if (!@\unlink($filename)) {
                throw new \RuntimeException(\sprintf("'%s': can not delete file"));
            }
        }
        if (false === ($this->handle = \fopen($filename, "a+"))) {
            throw new \RuntimeException(\sprintf("'%s': can not open file for writing"));
        }
    }

    /**
     * Force indentation of given lines (one tab = 4 spaces).
     */
    public function indent(string $input, int $tabs, bool $skipFirstList = false): string
    {
        $indentString = \str_repeat(" ", $tabs * self::INDENT_SIZE);
        if ($skipFirstList) {
            return \preg_replace('/[\n\r]+/', "$0".$indentString, $input);
        }
        return $indentString.\preg_replace('/[\n\r]+/', "$0".$indentString, $input);
    }

    /**
     * Append text to generated file
     */
    public function write(string $string): void
    {
        if (!$this->handle) {
            throw new \RuntimeException("File was closed");
        }
        \fwrite($this->handle, $string);
    }

    /**
     * Close file
     */
    public function close(): void
    {
        if ($this->handle) {
            @fclose($this->handle);
        }
        $this->handle = null;
    }
}
