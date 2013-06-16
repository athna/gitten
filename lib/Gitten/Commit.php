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
    /**
     * The repository this commit belongs to.
     * @var Repo
     */
    private $repo;

    /**
     * The commit hash.
     * @var Hash
     */
    private $commitHash;

    /**
     * The tree hash.
     * @var Hash
     */
    private $treeHash;

    /**
     * The parent hash.
     * @var Hash
     */
    private $parentHash;

    /**
     * The author date.
     * @var DateTime
     */
    private $authorDate;

    /**
     * The author.
     * @var Contact
     */
    private $author;

    /**
     * The committer date.
     * @var DateTime
     */
    private $committerDate;

    /**
     * The committer.
     * @var Contact
     */
    private $committer;

    /**
     * The subject.
     * @var string
     */
    private $subject;

    /**
     * Constructs a new commit info.
     *
     * @param Repo $repo
     *            The repository this commit belongs to.
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
    public function __construct(Repo $repo, Hash $commitHash, Hash $treeHash,
        Hash $parentHash, DateTime $authorDate, Contact $author,
        DateTime $committerDate, Contact $committer, $subject)
    {
        $this->repo = $repo;
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
     * @return Hash
     *            The commit hash.
     */
    public function getCommitHash()
    {
        return $this->commitHash;
    }

    /**
     * Returns the tree hash.
     *
     * @return Hash
     *            The tree hash.
     */
    public function getTreeHash()
    {
        return $this->treeHash;
    }

    /**
     * Returns the parent hash.
     *
     * @return Hash
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

    /**
     * Returns the commit URL.
     *
     * @return string
     *             The commit URL
     */
    public function getUrl()
    {
        return $this->repo->getCommitUrl($this);
    }

    /**
     * Returns the HTML code for this commit.
     *
     * @return string
     *             The HTML code for this commit.
     */
    public function getHTML()
    {
        return sprintf('<a href="%s">%s</a>', $this->getUrl(),
            $this->getCommitHash()->getHTML());
    }
}
