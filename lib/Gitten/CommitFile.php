<?php
/*
 * Copyright (C) 2013 Klaus Reimer <k@ailis.de>
 * See LICENSE.md for licensing information.
 */

namespace Gitten;

/**
 * A file in a commit.
 *
 * @author Klaus Reimer <k@ailis.de>
 */
class CommitFile
{
    /** The commit renderer. */
    private $renderer;

    /** The index inside the commit. */
    private $index;

    /** The filename. */
    private $filename;

    /** The change type (M, A, D, ...) */
    private $type;

    /** The source file mode. */
    private $srcMode;

    /** The destination file mode. */
    private $destMode;

    /** The source hash. */
    private $srcHash;

    /** The destination hash. */
    private $destHash;

    /** The number of deletions. */
    private $deletions;

    /** The number of additions. */
    private $additions;

    /** If file is binary or not. */
    private $binary;

    /**
     * Constructs a new commit file.
     *
     * @param CommitRenderer $renderer
     *            The commit renderer.
     * @param int $index
     *            The index inside the commit.
     * @param string $filename
     *            The filename.
     */
    public function __construct(CommitRenderer $renderer, $index, $filename)
    {
        $this->renderer = $renderer;
        $this->index = $index;
        $this->filename = $filename;
    }

    /**
     * Sets raw data.
     *
     * @param string $type
     *            The change type.
     * @param int $srcMode
     *            The source file mode.
     * @param int $destMode
     *            The destination file mode.
     * @param string $srcHash
     *            The source hash.
     * @param string $destHash
     *            The destination hash.
     */
    public function setRawData($type, $srcMode, $destMode, $srcHash,
        $destHash)
    {
        $this->type = strtolower($type);
        $this->srcMode = $srcMode;
        $this->destMode = $destMode;
        $this->destHash = $destHash;
        $this->srcHash = $srcHash;
    }

    /**
     * Sets num stat data.
     *
     * @param int $additions
     *            The number of additions.
     * @param int $deletions
     *            The number of deletions.
     * @param boolean $binary
     *            If file is binary or not.
     */
    public function setNumStatData($additions, $deletions, $binary)
    {
        $this->additions = $additions;
        $this->deletions = $deletions;
        $this->binary = $binary;
    }

    /**
     * Returns the index inside the commit.
     *
     * @return int
     *            The index.
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * Returns the filename.
     *
     * @return string
     *            The filename.
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Returns the change type.
     *
     * @return string
     *            The type.
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Returns the source file mode.
     *
     * @return int
     *            The source file mode.
     */
    public function getSrcMode()
    {
        return $this->srcMode;
    }

    /**
     * Returns the source file mode.
     *
     * @return int
     *            The source file mode.
     */
    public function getDestMode()
    {
        return $this->destMode;
    }

    /**
     * Returns the additions.
     *
     * @return int
     *            The additions.
     */
    public function getAdditions()
    {
        return $this->additions;
    }

    /**
     * Returns the deletions.
     *
     * @return int
     *            The deletions.
     */
    public function getDeletions()
    {
        return $this->deletions;
    }

    /**
     * Returns the modification (Additions plus deletions).
     *
     * @return int
     *            The modifications.
     */
    public function getModifications()
    {
        return $this->deletions + $this->additions;
    }

    /**
     * Returns the source hash.
     *
     * @return string
     *            The source hash.
     */
    public function getSrcHash()
    {
        return $this->srcHash;
    }

    /**
     * Returns the destination hash.
     *
     * @return string
     *            The destination hash.
     */
    public function getDestHash()
    {
        return $this->destHash;
    }

    /**
     * Returns the blob hash. This either the commit hash or (when file was
     * deleted) the parent hash.
     *
     * @return string
     *            The blob hash.
     */
    public function getBlobHash()
    {
        if ($this->type == "d")
            return $this->renderer->getParentHash();
        else
            return $this->renderer->getCommitHash();
    }

    /**
     * Returns the parent hash.
     *
     * @return string
     *            The parent hash.
     */
    public function getParentHash()
    {
        return $this->renderer->getParentHash();
    }

    /**
     * Returns the commit hash.
     *
     * @return string
     *            The commit hash.
     */
    public function getCommitHash()
    {
        return $this->renderer->getCommitHash();
    }

    /**
     * Returns the URL to the commit blob.
     *
     * @return string
     *             The commit blob url or NULL if blob was deleted.
     */
    public function getCommitBlobUrl()
    {
        if ($this->type == "d") return null;
        $revision = $this->getCommitHash();
        $path = $this->filename;
        $repoUrl = $this->renderer->getRepo()->getUrl();
        return "$repoUrl/blob/$revision/$path";
    }

    /**
     * Returns the URL to the parent blob.
     *
     * @return string
     *             The parent blob url or NULL if blob was added.
     */
    public function getParentBlobUrl()
    {
        if ($this->type == "a") return null;
        $revision = $this->getParentHash();
        $path = $this->filename;
        $repoUrl = $this->renderer->getRepo()->getUrl();
        return "$repoUrl/blob/$revision/$path";
    }


    /**
     * Checks if file is binary or not.
     *
     * @return boolean
     *            True if file is binary, false if not.
     */
    public function isBinary()
    {
        return $this->binary;
    }
}
