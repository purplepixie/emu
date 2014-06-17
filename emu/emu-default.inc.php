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

// emu-default.inc.php
// EMU Default Markup Set

class emu_default extends EMU_Markup
{
    function emu_default($n, $s, $f, $ns, $nf)
    {
        $this->name = $n;
        $this->start = $s;
        $this->finish = $f;
        $this->newStart = $ns;
        $this->newFinish = $nf;
    }

    
}

class emu_default_internal_link extends EMU_Markup
{
    public $route = "./";
    public $top = false;

    function emu_default_internal_link()
    {
        $this->name = "internal_link";
        $this->start = "\[";
        $this->finish = "\]";
        $this->process = EMU_Markup_Process::SNIPPET;
    }

    function Render($input)
    {
        $match = $input[1];
       
        $out = "";
        $tag = $match;
        $text = $match;
        $parts = preg_split("/\s+/", $match);
        //print_r($parts);
        if (count($parts)>1)
        {
            $tag = $parts[0];
            $text = "";
            for($i=1; $i<count($parts); ++$i)
            {
                if ($i>1)
                    $text.=" ";
                $text.=$parts[$i];
            }
        }

        $out = "<a href=\"".$this->route.$tag."\"";
        if ($this->top)
            $out.=" target=\"top\"";
        $out.=">".$text."</a>";
        
        return $out;
    }
}

class emu_default_external_link extends EMU_Markup
{
    public $top = false;
    private $internal_render_utility = null;

    function emu_default_external_link()
    {
        $this->name = "external_link";
        $this->start = "\[\[";
        $this->finish = "\]\]";
        $this->process = EMU_Markup_Process::SNIPPET;
        $this->internal_render_utility = new emu_default_internal_link();
        $this->internal_render_utility->route = "";
    }

    function Render($input)
    {
        $this->internal_render_utility->top = $this->top;
        return $this->internal_render_utility->Render($input);
    }
}

// MARKUP TAGS

$h3 = new emu_default(
    "h3", "\=\=\=\s", "\s\=\=\=", "<h3>", "</h3>"
    );
$h3->isTitle=true;
$h3->titleLevel=3;
EMU::AddMarkup($h3);

$h2 = new emu_default(
    "h2", "\=\=\s", "\s\=\=", "<h2>", "</h2>"
    );
$h2->isTitle=true;
$h2->titleLevel=2;
EMU::AddMarkup($h2);

$h1 = new emu_default(
    "h1", "\=\s", "\s\=", "<h1>", "</h1>"
    );
$h1->isTitle=true;
$h1->titleLevel=1;
EMU::AddMarkup($h1);

EMU::AddMarkup( new emu_default(
    "bold", "\*", "\*", "<b>", "</b>"
    ));

EMU::AddMarkup( new emu_default(
    "italic", "\/\/", "\/\/", "<i>", "</i>"
    ));

EMU::AddMarkup( new emu_default(
    "underline", "_", "_", "<u>", "</u>"
    ));

$paragraph = new emu_default(
    "paragraph", "\\\n\\\n", "", "<br /><br />", ""
    );
$paragraph->replaceType = EMU_Markup_Replace_Type::SINGLE;
EMU::AddMarkup($paragraph);

// LISTS

EMU::AddMarkup( new emu_default(
    "list", "\{", "\}", "<ul>", "</ul>"
    ));

EMU::AddMarkup( new emu_default(
    "list_item", "\+", "\\\n", "<li>", "</li>"
    ));

// LINKS

$external_link = new emu_default_external_link();
EMU::AddMarkup($external_link);

$internal_link = new emu_default_internal_link();
EMU::AddMarkup($internal_link);

?>