<?php
/*
 * Copyright (C) 2013 Klaus Reimer <k@ailis.de>
 * See LICENSE.md for licensing information.
 */

namespace Gitten;

final class DateTime
{
    /** The time as UNIX timestamp in seconds. */
    private $time;

    /**
     * Constructs a new datetime.
     *
     * @param number $time
     *            The time as UNIX timestamp in seconds.
     */
    public function __construct($time)
    {
        $this->time = $time;
    }

    /**
     * Returns the time as UNIX timestamp in seconds.
     *
     * @return number
     *             The time as UNIX timestamp in seconds.
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * Returns the time in human readable short text form.
     *
     * @return string
     *            The time in human readable short text form.
     */
    public function getShortText()
    {
        $seconds = $time = time() - $this->time;
        if ($seconds == 1) return "a second ago";
        if ($seconds < 60) return $seconds . " seconds ago";
        $minutes = round($time /= 60);
        if ($minutes == 1) return "a minute ago";
        if ($minutes < 60) return $minutes . " minutes ago";
        $hours = round($time /= 60);
        if ($hours == 1) return "an hour ago";
        if ($hours < 24) return $hours . " hours ago";
        $days = round($time /= 24);
        if ($days == 1) return "yesterday";
        if ($days < 31) return $days . " days ago";
        $months = round($time /= 30.45);
        if ($months == 1) return "a month ago";
        if ($months < 12) return $months . " months ago";
        $years = round($time / 12);
        return $years . " years ago";
    }

    /**
     * Returns the time in human readable long text form.
     *
     * @return string
     *            The time in human readable long text form.
     */
    public function getLongText()
    {
        return date("Y-m-j H:i:s", $this->time);
    }

    /**
     * Returns the HTML presentation of this time object.
     *
     * @return string
     *             The HTML code to present this time object.
     */
    public function getHTML()
    {
        return sprintf('<span class="datetime" title="%s">%s</span>',
            $this->getLongText(), $this->getShortText());
    }
}
