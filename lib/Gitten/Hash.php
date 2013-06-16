<?php
/*
 * Copyright (C) 2013 Klaus Reimer <k@ailis.de>
 * See LICENSE.md for licensing information.
 */

namespace Gitten;

/**
 * Container class for a SHA-1 hash.
 *
 * @author Klaus Reimer <k@ailis.de>
 */
final class Hash
{
    /**
     * The full SHA-1 hash as a string.
     * @var string
     */
    private $has;

    /**
     * Constructs a new SHA-1 hash.
     *
     * @param string $hash
     *            The full SHA-1 hash as a string.
     */
    public function __construct($hash)
    {
        $this->hash = $hash;
    }

    /**
     * Returns the full SHA-1 hash.
     *
     * @return string
     *             The full SHA-1 hash.
     */
    public function getFull()
    {
        return $this->hash;
    }

    /**
     * Returns the short SHA-1 hash.
     *
     * @return string
     *            The short SHA-1 hash.
     */
    public function getShort()
    {
        return substr($this->hash, 0, 7);
    }

    /**
     * Returns the HTML presentation of this hash object.
     *
     * @return string
     *             The HTML code to present this time object.
     */
    public function getHTML()
    {
        return sprintf('<span class="hash" title="%s">%s</span>',
            $this->getFull(), $this->getShort());
    }

    /**
     * Returns the string representation of this SHA-1 hash which is simply
     * the full hash.
     *
     * @return string
     *              The string representation of this SHA-1 hash.
     */
    public function __toString()
    {
        return $this->getFull();
    }
}
