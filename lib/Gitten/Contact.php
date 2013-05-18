<?php
/*
 * Copyright (C) 2013 Klaus Reimer <k@ailis.de>
 * See LICENSE.md for licensing information.
 */

namespace Gitten;

/**
 * A contact.
 *
 * @author Klaus Reimer <k@ailis.de>
 */
class Contact
{
    /** The name. */
    private $name;

    /** The email address. */
    private $email;

    /**
     * Constructs a new contact.
     *
     * @param string $name
     *            The name.
     * @param string $email
     *            The email address.
     */
    public function __construct($name, $email)
    {
        $this->name = $name;
        $this->email = $email;
    }

    /**
     * Returns the name.
     *
     * @return string
     *            The name.
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the email address.
     *
     * @return string
     *            The email address.
     */
    public function getEMail()
    {
        return $this->email;
    }

    /**
     * Returns the avatar HTML code.
     *
     * @param int $size
     *            The desired avatar size.
     * @return string
     *             The avatar HTML code.
     */
    public function getAvatarHTML($size = 16)
    {
        $hash = md5($this->email);
        $url = "//gravatar.com";
        $url .= "/avatar/$hash?s=$size";
        echo "<a class=\"gravatar\" href=\"http://www.gravatar.com/$hash\">";
        echo "<img src=\"$url\" alt=\"\" />";
        echo "</a>";
    }

    /**
     * Returns the HTML presentation of this time object.
     *
     * @return string
     *             The HTML code to present this time object.
     */
    public function getHTML()
    {
        return sprintf('<a class="contact" href="mailto:%s">%s</a>',
            htmlspecialchars($this->email),
            htmlspecialchars($this->name));
    }
}