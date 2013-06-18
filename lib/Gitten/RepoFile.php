<?php
/*
 * Copyright (C) 2013 Klaus Reimer <k@ailis.de>
 * See LICENSE.md for licensing information.
 */

namespace Gitten;

/**
 * A file or directory in a Git repository.
 *
 * @author Klaus Reimer <k@ailis.de>
 */
final class RepoFile extends File
{
    /** The repository. */
    private $repo;

    /** The path to the file relative to the repository root. */
    private $path;

    /** Cached file type (file or directory). Access with getType(). */
    private $type = null;

    /**
     * Cached file size (0 if directory). Access with getSize().
     * @var FileSize
     */
    private $size = null;

    /**
     * Cached file mode. Access with getMode().
     * @var FileMode
     */
    private $mode = null;

    /** Cached last commit. */
    private $lastCommit;

    /** The cached children. Access with getChildren(). */
    private $children = null;

    /** The cached content. Access with getContent(). */
    private $content = null;

    /**
     * The cached line count. Access with getLineCount().
     * @var int
     */
    private $lineCount = null;

    /**
     * Constructs a new repository file.
     *
     * @param \Gitten\LocalFile|\Gitten\Repo $repo
     *            The repository.
     * @param string $path
     *            The path to the repository file relative to the repository
     *            root. If not specified then the file points at the repository
     *            root.
     * @param string $type
     *            The file type ("file" or "directory"). If no path was
     *            specified then this defaults to "directory".
     * @param FileSize $size
     *            Optional file size. Read from repository if not specified.
     *            Ignored for directories.
     * @param FileMode $mode
     *            Optional file mode. Read from repository if not specified.
     *            Ignored for directories.
     */
    public function __construct(Repo $repo, $path = ".", $type = null,
        FileSize $size = null, FileMode $mode = null)
    {
        $this->repo = $repo;
        $this->path = $path ? $path : ".";
        $this->type = $path == "." ? "directory" : $type;
        $this->size = $this->type == "directory" ? new FileSize(0) : $size;
        $this->mode = $this->type == "directory" ? new FileMode(0755) : $mode;
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
     * @param string
     *            Optional revision. Defaults to currently selected revision.
     * @return string
     *            The tree URL.
     */
    public function getUrl($revision = null)
    {
        return $this->repo->getFileUrl($this, $revision);
    }

    /**
     * Returns the raw URL of this repository file.
     *
     * @return string
     *             The raw URL.
     */
    public function getRawUrl()
    {
        return $this->repo->getRawUrl($this);
    }

    /**
     * Returns the commit history URL of this repository file.
     *
     * @param int $page
     *             The page number. Defaults to 1.
     * @return string
     *             The commit history URL.
     */
    public function getCommitsUrl($page = 1)
    {
        return $this->repo->getCommitsUrl($this, $page);
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
        return $this->getType() == "directory";
    }

    /**
     * Checks if file is a file.
     *
     * @return boolean
     *            True if file is a file, false if not.
     */
    public function isFile()
    {
    	return $this->getType() == "file";
    }

    /**
     * Checks if file is not found.
     *
     * @return boolean
     *            True if file is not found, false if not.
     */
    public function isNotFound()
    {
    	return $this->getType() == "notfound";
    }

    /**
     * Returns the file size.
     *
     * @return FileSize
     *            The file size.
     */
    public function getSize()
    {
        if (is_null($this->size)) $this->readFileInfo();
        return $this->size;
    }

    /**
     * Returns the file mode.
     *
     * @return FileMode
     *            The file mode.
     */
    public function getMode()
    {
        if (is_null($this->mode)) $this->readFileInfo();
        return $this->mode;
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
     * @return LocalFile
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
     * @return LocalFile[]
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
     * Reads the file info from the Git repository.
     */
    private function readFileInfo()
    {
        $file = $this->repo->getFile($this->path);
        if ($file)
        {
	        $this->size = $file->getSize();
	        $this->mode = $file->getMode();
	        $this->type = $file->getType();
        }
        else
        {
        	$this->type = "notfound";
        	$this->mode = new FileMode(0755);
        	$this->size = new FileSize(0);
        }
    }

    /**
     * Returns the children files of this directory. Empty if there are
     * no children or if the current file is not a directory. The children
     * are sorted alphabetically with the directories at the top.
     *
     * @return RepoFile[] The children files. May be empty.
     */
    public function getChildren()
    {
        if (is_null($this->children))
        {
            $this->children = $this->repo->getChildren($this);
        }
        return $this->children;
    }

    /**
     * Returns the child with the specified name. Returns null if there is
     * no child with this name.
     *
     * @param string $name
     *            The name of the desired child.
     * @return RepoFile
     *            The child or null of not found.
     */
    public function getChild($name)
    {
        return $this->repo->getFile($this, $this->path . "/" . $name);
    }

    /**
     * Returns the file type.
     *
     * @return string
     *            The file type.
     */
    public function getType()
    {
        if (is_null($this->type)) $this->readFileInfo();
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

    /**
     * Returns the last modified timestamp.
     *
     * @return DateTime
     *             The last modified timestamp.
     */
    public function getLastModified()
    {
        return $this->getLastCommit()->getAuthorDate();
    }

    /**
     * Reads the raw content of this file.
     *
     * @return string
     *            The raw content of this file.
     */
    public function getContent()
    {
        if (is_null($this->content))
        {
            $this->content = $this->repo->readFile($this, $this->lineCount);
        }
        return $this->content;
    }

    /**
     * Returns the number of lines.
     *
     * @return number
     *            The number of lines.
     */
    public function getLineCount()
    {
        if (is_null($this->lineCount)) $this->readContent();
        return $this->lineCount;
    }

    /**
     * Returns the commits for this repository file.
     *
     * @param int $number
     *            Optional number of commits to return. Defaults to 35.
     * @param int $page
     *            The page to display. Defaults to 0.
     * @return Commit[]
     *            The commits.
     */
    public function getCommits($number = 35, $page = 1, &$hasMore = null)
    {
        return $this->repo->getCommits($this, $number, $page, $hasMore);
    }
}
