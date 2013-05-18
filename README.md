Gitten
======

Gitten is a [Git](http://git-scm.com/) web interface written in PHP.

Some features:

* Very few dependencies
* Easy installation
* Fully themable
* Supports multiple repositories


## Dependencies

You only need [PHP](http://www.php.net/) 5.2 or higher and the Git command
line tools.


## Installation

Just unpack (or clone) Gitten somewhere into you web root. If your `git`
executable is in your `PATH` and your Git repositories are located in `/git`
then you can simply navigate to the Gitten directory with your browser and
everything should work out-of-the-box.  Otherwise you need to provide a
small configuration as explained in the next section.


## Configuration

The Gitten configuration file `gitten.ini` can be found in the `config`
directory.  You can edit this file directly but it is recommended to first
copy it either to `gitten-local.ini` in the same directory or to
`/etc/gitten.ini`.  The reason is that you most likely don't want to
accidentaly overwrite the file when you install a new Gitten version.

Another option is setting the `GITTEN_CONFIG` environment variable if you
want a completely different configuration file location.

All configuration settings are documented in the INI file. The most
important setting is the `repoBase` setting.  If your Git repositories are
not located in `/git` then you have to enter your repository base path
there.  Another important setting is the `git` setting.  You must set it to
the location of your `git` executable if it is not located in the `PATH`.


## Layouts and Themes

Gitten is fully themable. The template files are simply PHP-spiced HTML
files locates in the `layout` directory.  You can choose the layout in the
Gitten configuration.

Each layout can contain multiple themes in the `themes` sub-directory of the
layout.  A theme provides the images and styles for the layout.  You can
choose the theme in the Gitten configuration.


## Pretty-URLs

By default URLs used by Gitten contain the `index.php` script file name to
ensure it is working without any URL rewriting configuration.  If you want
nicer URLs then you first need to enable
[mod_rewrite](http://httpd.apache.org/docs/current/mod/mod_rewrite.html) in
your Apache configuration.  Then copy the `.htaccess-example` file to
`.htaccess` and setup the `RewriteBase` value in it.  If you are not using
Apache or if your Apache doesn't support `.htaccess` files then you have to
configure the URL rewriting yourself.


## License

Copyright (C) 2013 Klaus Reimer <k@ailis.de>

This program is free software: you can redistribute it and/or modify it
under the terms of the [GNU General Public
License](http://www.gnu.org/licenses/gpl.html) as published by the Free
Software Foundation, either version 3 of the License, or (at your option)
any later version.

This program is distributed in the hope that it will be useful, but WITHOUT
ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
more details.
