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
    /** The repository. */
    private $repo;

    /** The commit. */
    private $commit;

    /** @var int The index inside the commit. */
    private $index;

    /** @var string The filename. */
    private $filename;

    /** @var string The change type (M, A, D, ...) */
    private $type;

    /** @var FileMode The source file mode. */
    private $srcMode;

    /** @var FileMode The destination file mode. */
    private $destMode;

    /** @var Hash The source hash. */
    private $srcHash;

    /** @var Hash The destination hash. */
    private $destHash;

    /** @var int The number of deletions. */
    private $deletions;

    /** @var int The number of additions. */
    private $additions;

    /** @var binary If file is binary or not. */
    private $binary;

    /**
     * Constructs a new commit file.
     *
     * @param Repo $repo
     *            The repository.
     * @param Commit $commit
     *            The commit.
     * @param int $index
     *            The index inside the commit.
     * @param string $filename
     *            The filename.
     */
    public function __construct(Repo $repo, Commit $commit, $index, $filename)
    {
        $this->repo = $repo;
        $this->commit = $commit;
        $this->index = $index;
        $this->filename = $filename;
    }

    /**
     * Sets raw data.
     *
     * @param string $type
     *            The change type.
     * @param FileMode $srcMode
     *            The source file mode.
     * @param FileMode $destMode
     *            The destination file mode.
     * @param Hash $srcHash
     *            The source hash.
     * @param Hash $destHash
     *            The destination hash.
     */
    public function setRawData($type, FileMode $srcMode, FileMode $destMode,
        Hash $srcHash, Hash $destHash)
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
     * Check if file was deleted.
     *
     * @return boolean
     *            True if file was deleted, false if not.
     */
    public function isDeletion()
    {
        return $this->type == "d";
    }

    /**
     * Check if file was added.
     *
     * @return boolean
     *            True if file was added, false if not.
     */
    public function isAddition()
    {
        return $this->type == "a";
    }

    /**
     * Check if file was modified.
     *
     * @return boolean
     *            True if file was modified,false if not.
     */
    public function isModification()
    {
        return $this->type == "m";
    }

    /**
     * Returns the source file mode.
     *
     * @return FileMode
     *            The source file mode.
     */
    public function getSrcMode()
    {
        return $this->srcMode;
    }

    /**
     * Returns the source file mode.
     *
     * @return FileMode
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
     * @return Hash
     *            The source hash.
     */
    public function getSrcHash()
    {
        return $this->srcHash;
    }

    /**
     * Returns the destination hash.
     *
     * @return Hash
     *            The destination hash.
     */
    public function getDestHash()
    {
        return $this->destHash;
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

    /**
     * Returns the URL to the source file.
     *
     * @return string
     *             The URL to the source file.
     */
    public function getSrcUrl()
    {
        return $this->repo->getBlobUrl($this->filename,
            $this->commit->getParentHash()->getFull());
    }

    /**
     * Returns the URL to the destination file.
     *
     * @return string
     *             The URL to the destination file.
     */
    public function getDestUrl()
    {
        return $this->repo->getBlobUrl($this->filename,
            $this->commit->getCommitHash()->getFull());
    }

    /**
     * Returns the URL to the source file if file was deleted or the
     * destination file if file was added or modified.
     *
     * @return string
     *            The URL to the source or destination file depending on
     *            the change type.
     */
    public function getUrl()
    {
        if ($this->isDeletion())
            return $this->getSrcUrl();
        else
            return $this->getDestUrl();
    }
}
