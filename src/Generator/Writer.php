<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalizer\Generator;

/**
 * File writer.
 */
class Writer
{
    const INDENT_SIZE = 4;

    /** @var resource */
    protected $handle;

    /** @var int */
    protected $indentation = 0;

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
     * Create in-memory buffer stream
     */
    public static function memory(): MemoryWriter
    {
        return new MemoryWriter();
    }

    /**
     * Force indentation of given lines (one tab = 4 spaces).
     */
    public static function indent(string $input, int $tabs, bool $skipFirstList = false): string
    {
        $indentString = \str_repeat(" ", $tabs * self::INDENT_SIZE);
        if ($skipFirstList) {
            return \preg_replace('/[\n\r]+/', "$0".$indentString, $input);
        }
        return $indentString.\preg_replace('/[\n\r]+/', "$0".$indentString, $input);
    }

    /**
     * Reset indentation
     */
    public function indentationReset(int $howMuch = 0): void
    {
        $this->indentation = \abs($howMuch);
    }

    /**
     * Increment indentation
     */
    public function indentationInc(int $howMuch = 1): void
    {
        $this->indentation += \abs($howMuch);
    }

    /**
     * Decrement indentation
     */
    public function indentationDec(int $howMuch = 1): void
    {
        $this->indentation = \max([0, $this->indentation - \abs($howMuch)]);
    }

    /**
     * Append text to generated file
     */
    private function doWrite(string $string): void
    {
        if (!$this->handle) {
            throw new \RuntimeException("File was closed");
        }
        \fwrite($this->handle, $string);
    }

    /**
     * Append text to generated file
     */
    public function write(string $string): void
    {
        if ($this->indentation) {
            $string = self::indent($string, $this->indentation);
        }
        $this->doWrite($string);
        $this->doWrite("\n");
    }

    /**
     * Write new line
     */
    public function newline(): void
    {
        $this->doWrite("\n");
    }

    /**
     * Append text to generated file
     */
    public function writeInline(string $string): void
    {
        $this->doWrite($string);
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

/**
 * In memory temporary writer
 */
final class MemoryWriter extends Writer
{
    /**
     * Default constructor
     */
    public function __construct()
    {
        $this->handle = \fopen('php://memory', 'w');
    }

    /**
     * Get current buffer
     */
    public function getBuffer(): string
    {
        if (!$this->handle) {
            throw new \RuntimeException("File was closed");
        }
        \rewind($this->handle);
        try {
            return \stream_get_contents($this->handle);
        } finally {
            $this->close();
        }
    }
}
