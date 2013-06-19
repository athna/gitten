<?php
/*
 * Copyright (C) 2013 Klaus Reimer <k@ailis.de>
 * See LICENSE.md for licensing information.
 */

namespace Gitten;

/**
 * A line in a diff chunk.
 *
 * @author Klaus Reimer <k@ailis.de>
 */
class DiffChunkLine
{
    /**
     * The diff chunk line type. '+' for addition, '-' for deletion or ' ' for
     * a none.
     */
    private $type;

    /** @var string The source code line. */
    private $line;

    /**
     * Constructs a new diff chunk line
     *
     * @param string $type
     *            The line type.
     * @param string $line
     *            The source code line.
     */
    public function __construct($type, $line)
    {
        $this->type = $type;
        $this->line = $line;
    }

    /**
     * Returns the line type.
     *
     * @return string
     *            The line type.
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Returns the line of source code.
     *
     * @return int
     *            The source code line.
     */
    public function getLine()
    {
        return $this->line;
    }

    /**
     * Check if line is an addition.
     *
     * @return boolean
     *            True if line is an addition.
     */
    public function isAddition()
    {
        return $this->type == '+';
    }

    /**
     * Check if line is a deletion.
     *
     * @return boolean
     *            True if line is a deletion.
     */
    public function isDeletion()
    {
        return $this->type == '-';
    }
}
