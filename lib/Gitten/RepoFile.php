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

    /** The cached children. Access with getChildren(). */
    private $children = null;

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
        return $this->repo->getChild($this, $name);
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

    /**
     * Reads the raw content of this file.
     *
     * @return string
     *            The raw content of this file.
     */
    public function read()
    {
        return $this->repo->readFile($this);
    }

    /**
     * Returns the HTML code of the README in this dirctory. Returns null
     * if this repository file is not a directory or if it does not contain
     * a README file.
     *
     * @return string
     *            The HTML code of the README in this directory or null
     *            if current file is not a directory or there is no README.
     */
    public function getReadmeHTML()
    {
        $children = $this->getChildren();
        foreach ($children as $child)
        {
            $name = strtolower($child->getName());
            if ($name == "readme.md")
            {
                return \Michelf\Markdown::defaultTransform($child->read());
            }
            else if ($name == "readme" || $name == "readme.txt")
            {
                return "<p>" . nl2br(htmlspecialchars($child->read())) . "</p>";
            }
            else if ($name == "readme.html" || $name == "readme.htm")
            {
            	return strip_tags($child->read(), "<h1><h2><h3><h4><h5>"
                    . "<h6><p><code><pre><strong><em><i><b><br><ul><ol>"
            		. "<li><a><img>");
            }
        }
        return null;
    }
}
