<?php
/*
 * Copyright (C) 2013 Klaus Reimer <k@ailis.de>
 * See LICENSE.md for licensing information.
 */

namespace Gitten;

/**
 * Container class for file sizes.
 *
 * @author Klaus Reimer <k@ailis.de>
 */
final class FileSize
{
    /** The file size in bytes. */
    private $size;

    /**
     * Constructs a new file.
     *
     * @param number $size
     *            The file size in bytes.
     */
    public function __construct($size)
    {
        $this->size = $size;
    }

    /**
     * Returns the file size in bytes.
     *
     * @return number
     *             The file size in bytes.
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Returns the size in human readable short text form.
     *
     * @return string
     *            The size in human readable short text form.
     */
    public function getShortText()
    {
        $size = $this->size;
        $units = "KMGTPEZY";
        $unit = -1;
        while ($size > 1024 && $unit < strlen($units) - 1)
        {
            $size /= 1024;
            $unit += 1;
        }
        if ($unit >= 0)
        {
            return number_format(ceil($size * 10) / 10, 1) . " " .
                $units[$unit] . "iB";
        }
        else
        {
            return $size . " B";
        }
    }

    /**
     * Returns the size in human readable long text form.
     *
     * @return string
     *            The size in human readable long text form.
     */
    public function getLongText()
    {
        return number_format($this->size, 0) . " B";
    }
}
