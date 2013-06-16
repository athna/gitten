<?php
/*
 * Copyright (C) 2013 Klaus Reimer <k@ailis.de>
 * See LICENSE.md for licensing information.
 */

namespace Gitten;

/**
 * Info about a commit.
 *
 * @author Klaus Reimer <k@ailis.de>
 */
class Commit
{
    /** The commit hash. */
    private $commitHash;

    /** The tree hash. */
    private $treeHash;

    /** The parent hash. */
    private $parentHash;

    /** The author date (Unix timestamp). */
    private $authorDate;

    /** the author. */
    private $author;

    /** The committer date (Unix timestamp). */
    private $committerDate;

    /** The committer. */
    private $committer;

    /** The subject. */
    private $subject;

    /**
     * Constructs a new commit info.
     *
     * @param string $commitHash
     *            The commit hash.
     * @param string $treeHash
     *            The tree hash.
     * @param string $parentHash
     *            The parent hash.
     * @param DateTime $authorDate
     *            The author date.
     * @param Contact $author
     *            The author.
     * @param DateTime $committerDate
     *            The committer date.
     * @param Contact $committer
     *            The committer.
     * @param string $subject
     *            The subject.
     */
    public function __construct($commitHash, $treeHash,
        $parentHash, DateTime $authorDate, Contact $author,
        DateTime $committerDate, Contact $committer, $subject)
    {
        $this->commitHash = $commitHash;
        $this->treeHash = $treeHash;
        $this->parentHash = $parentHash;
        $this->authorDate = $authorDate;
        $this->author = $author;
        $this->committerDate = $committerDate;
        $this->committer = $committer;
        $this->subject = trim($subject);
    }

    /**
     * Returns the commit hash.
     *
     * @return string
     *            The commit hash.
     */
    public function getCommitHash()
    {
        return $this->commitHash;
    }

    /**
     * Returns the tree hash.
     *
     * @return string
     *            The tree hash.
     */
    public function getTreeHash()
    {
        return $this->treeHash;
    }

    /**
     * Returns the parent hash.
     *
     * @return string
     *            The parent hash.
     */
    public function getParentHash()
    {
        return $this->parentHash;
    }

    /**
     * Returns the author date.
     *
     * @return DateTime
     *            The author date.
     */
    public function getAuthorDate()
    {
        return $this->authorDate;
    }

    /**
     * Returns the author.
     *
     * @return Contact
     *            The author.
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Returns the committer date.
     *
     * @return DateTime
     *            The committer date.
     */
    public function getCommitterDate()
    {
        return $this->committerDate;
    }

    /**
     * Returns the committer.
     *
     * @return Contact
     *            The committer.
     */
    public function getCommitter()
    {
        return $this->committer;
    }

    /**
     * Returns the subject.
     *
     * @return string
     *            The subject.
     */
    public function getSubject()
    {
        return $this->subject;
    }
}