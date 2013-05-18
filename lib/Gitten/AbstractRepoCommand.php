<?php
/*
 * Copyright (C) 2013 Klaus Reimer <k@ailis.de>
 * See LICENSE.md for licensing information.
 */

namespace Gitten;

abstract class AbstractRepoCommand extends AbstractCommand
{
    /** The repository file for which the command is executed. */
    protected $repoFile;

    /** The repository. */
    protected $repo;

    /**
     * Returns the file for which the command is executed.
     *
     * @return RepoFile
     *            The file.
     */
    public function getRepoFile()
    {
        return $this->repoFile;
    }

    /**
     * Returns the repository.
     *
     * @return Repo
     *            The repository.
     */
    public function getRepo()
    {
        return $this->repo;
    }

    /**
     * Returns the selected revision.
     *
     * @return string
     *             The selected revision.
     */
    public function getRevision()
    {
        return $this->revision;
    }

    /**
     * Processes the command arguments.
     *
     * @param string[] $args
     *           The command arguments to process.
     */
    protected function processArgs($args)
    {
        if (count($args))
            $revision = array_shift($args);
        else
            $revision = null;
        $this->repo = new Repo($this->getFile(), $revision);
        $path = trim(implode("/", $args), "/");
        if ($path == "")
            $this->repoFile = new RepoFile($this->repo);
        else
            $this->repoFile = new RepoFile($this->repo, $path);
    }
}
