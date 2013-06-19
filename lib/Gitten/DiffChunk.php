<?php
/*
 * Copyright (C) 2013 Klaus Reimer <k@ailis.de>
 * See LICENSE.md for licensing information.
 */

namespace Gitten;

/**
 * A diff chunk.
 *
 * @author Klaus Reimer <k@ailis.de>
 */
class DiffChunk
{
    /** @var int The starting line number in the source file. */
    private $sourceLine;

    /** @var int The number of lines in the source file chunk. */
    private $sourceLines;

    /** @var int The starting line number in the destination file. */
    private $destLine;

    /** @var int The number of lines in the destination file chunk. */
    private $destLines;

    /**
     * Constructs a new diff chunk.
     *
     * @param int $sourceLine
     *            The starting line number in the source file.
     * @param int $sourceLines
     *            The number of lines in the source file chunk.
     * @param int $destLine
     *            The starting line number in the destination file.
     * @param int $destLines
     *            The number of lines in the destination file chunk.
     */
    public function __construct($sourceLine, $sourceLines, $destLine,
        $destLines)
    {
        $this->sourceLine = $sourceLine;
        $this->sourceLines = $sourceLines;
        $this->destLine = $destLine;
        $this->destLines = $destLines;
    }

    /**
     * Returns the starting line number in the source file.
     *
     * @return int
     *            The starting line number in the source file.
     */
    public function getSourceLine()
    {
        return $this->sourceLine;
    }

    /**
     * Returns the number of lines of the chunk in the source file.
     *
     * @return int
     *            The number of chunk lines in the source file.
     */
    public function getSourceLines()
    {
        return $this->sourceLines;
    }

    /**
     * Returns the starting line number in the destination file.
     *
     * @return int
     *            The starting line number in the destination file.
     */
    public function getDestLine()
    {
        return $this->sourceLine;
    }

    /**
     * Returns the number of lines of the chunk in the destination file.
     *
     * @return int
     *            The number of chunk lines in the destination file.
     */
    public function getDestLines()
    {
        return $this->sourceLines;
    }
}
