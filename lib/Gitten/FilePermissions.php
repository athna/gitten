<?php
/*
 * Copyright (C) 2013 Klaus Reimer <k@ailis.de>
 * See LICENSE.md for licensing information.
 */

namespace Gitten;

final class FilePermissions
{
    /** The file permissions as numeric value. */
    private $value;

    /**
     * Constructs new file permissions.
     *
     * @param number $value
     *            The file permissions as numeric value.
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * Returns the file permissions as numeric value.
     *
     * @return number
     *             The file permissions as numeric value.
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Returns the file permissions in human readable short text form.
     *
     * @param boolean $setUidGid
     *            If setuid or setgid flag is set. If this is true then the
     *            execute flag is represented as "s" and "S" instead of
     *            "x" and "-".
     * @param boolean $sticky
     *            If sticky flag is set. If this is true then the
     *            execute flag is represented as "t" and "T" instead of
     *            "x" and "-".
     * @return string
     *            The file permissions in human readable short text form.
     */
    public function getShortText($setUidGid = false, $sticky = false)
    {
        $text = ($this->isRead() ? "r" : "-") . ($this->isWrite() ? "w" : "-");
        if ($sticky)
            $text .= $this->isExecute() ? "t" : "T";
        else if ($setUidGid)
            $text .= $this->isExecute() ? "s" : "S";
        else
            $text .= $this->isExecute() ? "x" : "-";
        return $text;
    }

    /**
     * Returns the file permissions in human readable long text form.
     *
     * @return string
     *            The file permissions in human readable long text form.
     */
    public function getLongText()
    {
        $perms = array();
        if ($this->isRead()) $perms[] = "Read";
        if ($this->isWrite()) $perms[] = "Write";
        if ($this->isExecute()) $perms[] = "Execute";
        if (count($perms)) return implode(" / ", $perms);
        return "None";
    }

    /**
     * Check for read permission.
     *
     * @return boolean
     *            True if read permission is set, false if not.
     */
    public function isRead()
    {
        return $this->value & 4;
    }

    /**
     * Check for write permission.
     *
     * @return boolean
     *            True if write permission is set, false if not.
     */
    public function isWrite()
    {
        return $this->value & 2;
    }

    /**
     * Check for execute permission.
     *
     * @return boolean
     *            True if execute permission is set, false if not.
     */
    public function isExecute()
    {
        return $this->value & 1;
    }
}
