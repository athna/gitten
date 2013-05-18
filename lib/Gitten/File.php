<?php
/*
 * Copyright (C) 2013 Klaus Reimer <k@ailis.de>
 * See LICENSE.md for licensing information.
 */

namespace Gitten;

abstract class File
{
    /**
     * Returns the path to this file. The path is relative to the repository
     * base directory. When the file is the repository base directory itself
     * then this method returns a dot character.
     *
     * @return string
     *            The path to this file relative to the repository base
     *            directory.
     */
    abstract function getPath();

    abstract function getUrl();

    /**
     * Returns the file name.
     *
     * @return string
     *             The file name.
     */
    abstract function getName();

    /**
     * Checks if file is a directory.
     *
     * @return boolean
     *            True if file is a directory, false if not.
     */
    abstract function isDirectory();

    /**
     * Returns the file size.
     *
     * @return FileSize
     *            The file size.
     */
    abstract function getSize();

    /**
     * Returns the last modified time.
     *
     * @return DateTime
     *            The last modified time.
     */
    abstract function getLastModified();

    /**
     * Checks if this is the root directory (The one pointing at the
     * repository base directory).
     *
     * @return boolean
     *             True if root directory, false if not.
     */
    abstract function isRoot();

    /**
     * Returns the parent directory. Null if there is no parent because this
     * is the root directory.
     *
     * @return File
     *            The parent directory or null if none.
     */
    abstract function getParent();

    /**
     * Returns all parent directories without the root directory.
     *
     * @return File[]
     *             All parent directories.
     */
    abstract function getParents();

    /**
     * Returns the children files of this directory. Empty if there are
     * no children or if the current file is not a directory. The children
     * are sorted alphabetically with the directories at the top.
     *
     * @return LocalFile[] The children files. May be empty.
     */
    abstract function getChildren();

    /**
     * Returns the child file with the specified name.
     *
     * @param string $name
     *            The name of the child.
     * @return LocalFile
     *            The child file.
     */
    abstract function getChild($name);

    /**
     * Returns the file type.
     *
     * @return string
     *            The file type.
     */
    abstract function getType();

    /**
     * Reads the raw content of this file and returns it.
     * 
     * @return string
     *             The raw file content.
     */
    abstract function read();

    /**
     * Returns the HTML code of the README in this dirctory. Returns null
     * if this repository file is not a directory or if it does not contain
     * a README file.
     *
     * @return string
     *            The HTML code of the README in this directory or null
     *            if current file is not a directory or there is no README.
     */
    public final function getReadMeHTML()
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
