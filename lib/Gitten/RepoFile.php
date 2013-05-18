<?php
/*
 * Copyright (C) 2013 Klaus Reimer <k@ailis.de>
 * See LICENSE.md for licensing information.
 */

namespace Gitten;

final class RepoFile
{
    /** The repository. */
    private $repo;

    /** The path to the file relative to the repository root. */
    private $path;

    /** The file type (file or directory). */
    private $type;

    /** The file size (0 if if directory). */
    private $size;

    /** The file mode. */
    private $mode;

    /** Cached last commit. */
    private $lastCommit;

    /**
     * Constructs a new repository file.
     *
     * @param File $repo
     *            The repository.
     * @param string $path
     *            The path to the repository file relative to the repository
     *            root. If not specified then the file points at the repository
     *            root.
     */
    public function __construct(Repo $repo, $path = ".", $type = "directory",
        $size = 0, $mode = 040000)
    {
        $this->repo = $repo;
        $this->path = $path ? $path : ".";
        $this->type = $type;
        $this->size = $size;
        $this->mode = $mode;
    }

    /**
     * Returns the path to this repository file. The path is relative to the
     * repository root. When the file is the repository root itself
     * then this method returns a dot character.
     *
     * @return string
     *            The path to this repository file relative to the repository
     *            root.
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Returns the tree URL of this repository file.
     *
     * @return string
     *            The tree URL.
     */
    public function getUrl()
    {
        return PHP_BASEURL . "/" . $this->repo->getFileUrl($this);
    }

    /**
     * Returns the repository file name.
     *
     * @return string
     *             The repository file name.
     */
    public function getName()
    {
        return $this->isRoot() ? $this->repo->getName() : basename($this->path);
    }

    /**
     * Checks if file is a directory.
     *
     * @return boolean
     *            True if file is a directory, false if not.
     */
    public function isDirectory()
    {
        return $this->type == "directory";
    }

    /**
     * Returns the file size.
     *
     * @return FileSize
     *            The file size.
     */
    public function getSize()
    {
        return new FileSize($this->size);
    }

    /**
     * Checks if this is the repository root.
     *
     * @return boolean
     *             True if root directory, false if not.
     */
    public function isRoot()
    {
        return $this->path == ".";
    }

    /**
     * Returns the parent directory. Null if there is no parent because this
     * is the root directory.
     *
     * @return File
     *            The parent directory or null if none.
     */
    public function getParent()
    {
        if ($this->isRoot()) return null;
        return new RepoFile($this->repo, dirname($this->path));
    }

    /**
     * Returns all parent directories.
     *
     * @return File[]
     *             All parent directories.
     */
    public function getParents()
    {
        $parents = array();
        if ($this->isRoot()) return $parents;
        $parent = $this->getParent();
        while ($parent)
        {
            $parents[] = $parent;
            $parent = $parent->getParent();
        }
        return array_reverse($parents);
    }

    /**
     * Returns the children files of this directory. Empty if there are
     * no children or if the current file is not a directory. The children
     * are sorted alphabetically with the directories at the top.
     *
     * @return File[] The children files. May be empty.
     */
    public function getChildren()
    {
        return $this->repo->getChildren($this);
    }

    /**
     * Returns the file type.
     *
     * @return string
     *            The file type.
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Returns the last commit for this file.
     *
     * @return Commit
     *            The last commit.
     */
    public function getLastCommit()
    {
        if (!$this->lastCommit)
            $this->lastCommit = $this->repo->getLastCommit($this);
        return $this->lastCommit;
    }
}
