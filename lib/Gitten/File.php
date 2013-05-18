<?php
/*
 * Copyright (C) 2013 Klaus Reimer <k@ailis.de>
 * See LICENSE.md for licensing information.
 */

namespace Gitten;

final class File
{
    /** The path to the file relative to the repository base directory. */
    private $path;

    /**
     * Constructs a new file.
     *
     * @param string $path
     *            The path to the file relative to the repository base
     *            directory. If not specified then the file points
     *            at the repository base directory.
     */
    public function __construct($path = ".")
    {
        $this->path = $path;
    }

    /**
     * Returns the path to this file. The path is relative to the repository
     * base directory. When the file is the repository base directory itself
     * then this method returns a dot character.
     *
     * @return string
     *            The path to this file relative to the repository base
     *            directory.
     */
    public function getPath()
    {
        return $this->path;
    }

    public function getUrl()
    {
        return PHP_BASEURL . "/" . $this->path;
    }

    /**
     * Returns the file name.
     *
     * @return string
     *             The file name.
     */
    public function getName()
    {
        return basename($this->path);
    }

    /**
     * Returns the absolute path to the file.
     *
     * @return string
     *            The absolute path to the file.
     */
    public function getAbsolutePath()
    {
        global $cfg;

        $repoBase = $cfg->getRepoBase();
        if ($this->isRoot())
            return rtrim($repoBase, "/");
        else
            return rtrim($repoBase, "/") . "/" . $this->path;
    }

    /**
     * Checks if file is a directory.
     *
     * @return boolean
     *            True if file is a directory, false if not.
     */
    public function isDirectory()
    {
        return is_dir($this->getAbsolutePath());
    }

    /**
     * Checks if this file is a Git repository.
     *
     * @return boolean
     *            True if file is a Git repository, false if not.
     */
    public function isRepository()
    {
        $absPath = $this->getAbsolutePath();
        if (!is_dir($absPath)) return false;
        if (preg_match("/.*\\.git\$/", $this->path)) return true;
        if (is_dir($absPath . "/.git")) return true;
        return false;
    }

    /**
     * Returns the file size.
     *
     * @return FileSize
     *            The file size.
     */
    public function getSize()
    {
        return new FileSize(filesize($this->getAbsolutePath()));
    }

    /**
     * Returns the last modified time.
     *
     * @return DateTime
     *            The last modified time.
     */
    public function getLastModified()
    {
        return new DateTime(filemtime($this->getAbsolutePath()));
    }

    /**
     * Checks if this is the root directory (The one pointing at the
     * repository base directory).
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
        return new File(dirname($this->path));
    }

    /**
     * Returns all parent directories without the root directory.
     *
     * @return File[]
     *             All parent directories.
     */
    public function getParents()
    {
        $parents = array();
        if ($this->isRoot()) return $parents;
        $parent = $this->getParent();
        while (!$parent->isRoot())
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
        $children = array();
        if (!$this->isDirectory()) return $children;
        $dir = opendir($this->getAbsolutePath());
        while ($filename = readdir($dir))
        {
            if ($filename == "." || $filename == "..") continue;
            $children[] = new File($this->path . "/" . $filename);
        }
        usort($children, function($a, $b) {
            if ($a->isDirectory() && !$b->isDirectory()) return -1;
            if (!$a->isDirectory() && $b->isDirectory()) return 1;
            return $a->getName() > $b->getName() ? 1 : -1;
        });
        return $children;
    }

    /**
     * Returns the child file with the specified name.
     *
     * @param string $name
     *            The name of the child.
     * @return File
     *            The child file.
     */
    public function getChild($name)
    {
        if ($this->isRoot())
            return new File($name);
        else
            return new File($this->path . "/" . $name);
    }

    /**
     * Returns the file type.
     *
     * @return string
     *            The file type.
     */
    public function getType()
    {
        if ($this->isRepository()) return "repository";
        if ($this->isDirectory()) return "directory";
        return "file";
    }
}
