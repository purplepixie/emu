<?php
/***
    EMU - EMU Mark Up
    Markup Syntax and Parser to HTML
    Copyright (C) 2014 David Cutting, http://www.purplepixe.org/dave/

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
***/

// index.php
// EMU Example

error_reporting(E_ALL);
ini_set('display_errors',1);

include_once("emu.inc.php");
include_once("emu-default.inc.php");

$text = file_get_contents("test.txt");


echo "<pre>".$text."</pre><br /><br /><br />";

echo EMU::Render($text);
?>