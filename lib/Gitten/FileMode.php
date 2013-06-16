<?php
/*
 * Copyright (C) 2013 Klaus Reimer <k@ailis.de>
 * See LICENSE.md for licensing information.
 */

namespace Gitten;

/**
 * Container class for file mode.
 *
 * @author Klaus Reimer <k@ailis.de>
 */
final class FileMode
{
    /** The file mode as numeric value. */
    private $value;

    /**
     * Constructs a new file mode.
     *
     * @param number $value
     *            The file mode as numeric value.
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * Returns the file mode as numeric value.
     *
     * @return number
     *             The file mode as numeric value.
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Returns the file mode in human readable short text form.
     *
     * @return string
     *            The file mode in human readable short text form.
     */
    public function getShortText()
    {
        return $this->getTypeCharacter()
            . $this->getUserPermissions()->getShortText($this->isSetUid())
            . $this->getGroupPermissions()->getShortText($this->isSetGid())
            . $this->getOtherPermissions()->getShortText(false, $this->isSticky());
    }

    /**
     * Returns the file mode in human readable long text form.
     *
     * @return string
     *            The file mode in human readable long text form.
     */
    public function getLongText()
    {
        $flags = array();
        if ($this->isSetUid()) $flags[] = "Set-User-ID";
        if ($this->isSetGid()) $flags[] = "Set-Group-ID";
        if ($this->isSticky()) $flags[] = "Sticky";
        return "Type: " . $this->getType() . "\r\n"
            . "Flags: " . (count($flags) ? implode(" / ", $flags) : "None")
            . "\r\nPermissions:\r\n"
            . "  User: " . $this->getUserPermissions()->getLongText() . "\r\n"
            . "  Group: " . $this->getGroupPermissions()->getLongText() . "\r\n"
            . "  Others: " . $this->getOtherPermissions()->getLongText();
    }

    /**
     * Returns the file type.
     *
     * @return string
     *            The file type.
     */
    public function getType()
    {
        switch ($this->value >> 12)
        {
            case 1:
                return "FIFO";
            case 2:
                return "Character device";
            case 4:
                return "Directory";
            case 6:
                return "Block device";
            case 8:
                return "File";
            case 10:
                return "Symbolic link";
            case 12:
                return "Socket";
            default:
                return "Unknown";
        }
    }

    /**
     * Returns the file type as a character.
     *
     * @return string
     *            The file type as a character.
     */
    public function getTypeCharacter()
    {
        switch ($this->value >> 12)
        {
            case 1:
                return "p";
            case 2:
                return "c";
            case 4:
                return "d";
            case 6:
                return "b";
            case 8:
                return "-";
            case 10:
                return "l";
            case 12:
                return "s";
            default:
                return "?";
        }
    }

    /**
     * Returns the user permissions.
     *
     * @return FilePermissions
     *            The user permissions.
     */
    public function getUserPermissions()
    {
        return new FilePermissions($this->value >> 6);
    }

    /**
     * Returns the group permissions.
     *
     * @return FilePermissions
     *            The group permissions.
     */
    public function getGroupPermissions()
    {
        return new FilePermissions($this->value >> 3);
    }

    /**
     * Returns the other permissions.
     *
     * @return FilePermissions
     *            The other permissions.
     */
    public function getOtherPermissions()
    {
        return new FilePermissions($this->value);
    }

    /**
     * Checks if setuid flag is set.
     *
     * @return boolean
     *            True if setuid flag is set, false if not.
     */
    public function isSetUID()
    {
        return $this->value & 04000;
    }

    /**
     * Checks if setgid flag is set.
     *
     * @return boolean
     *            True if setgid flag is set, false if not.
     */
    public function isSetGID()
    {
        return $this->value & 02000;
    }

    /**
     * Checks if sticky bit is set.
     *
     * @return boolean
     *            True if sticky bit is set, false if not.
     */
    public function isSticky()
    {
        return $this->value & 01000;
    }
}
