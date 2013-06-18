<?php
/*
 * Copyright (C) 2013 Klaus Reimer <k@ailis.de>
 * See LICENSE.md for licensing information.
 */

namespace Gitten;

/**
 * A commit diff.
 *
 * @author Klaus Reimer <k@ailis.de>
 */
class CommitDiff
{
    /** The repository. */
    private $repo;

    /** The Git pipe to read data from. */
    private $pipe;

    /**
     * Constructs a new commit renderer.
     *
     * @param Repo $repo
     *            The repository.
     */
    public function __construct(Repo $repo, $pipe)
    {
        $this->repo = $repo;
        $this->pipe = $pipe;
        $this->parseHeader();
    }

    /**
     * Parses the git diff header from the Git pipe.
     */
    private function parseHeader()
    {

    }
}
