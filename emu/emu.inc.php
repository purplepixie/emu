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

// emu.inc.php
// Main EMU Class Include


class EMU
{
	public static $version = "0.01";
	private static $markup = array();
	private static $headings = array();
	
	public static $generateHeadingList = true;
	private static $headingList = array();

	public static function AddMarkup($newMarkup)
	{
		self::$markup[$newMarkup->name]=$newMarkup;
	}

	public static function Render($text)
	{
		foreach(self::$markup as $key => $mark)
		{
			switch($mark->process)
			{
				case EMU_Markup_Process::NONE:
				case EMU_Markup_Process::SNIPPET:
					$start = $mark->start;
					$finish = $mark->finish;
					if ($mark->regexType == EMU_Markup_Regex_Type::PLAIN) // otherwise pre marked up
					{
						$start = preg_quote($start);
						$finish = preg_quote($finish);
					}

					if ($mark->replaceType == EMU_Markup_Replace_Type::SINGLE)
					{
						$search = "/".$start."/U";
						$replace = $mark->newStart;
					}
					else // NORMAL
					{
						$search = "/".$start."(.*)".$finish."/U";
						$replace = $mark->newStart."$1".$mark->newFinish;
					}
					//echo $search." :: ".$replace;

					// TITLE SEARCHING
					if (self::$generateHeadingList)
					{
						if ($mark->isTitle)
						{
							$titles = array();
							$titletext = array();
							$result = preg_match_all($search, $text, $titles, PREG_OFFSET_CAPTURE);
							if ($result)
							{
								preg_match_all($search, $text, $titletext); // necessary to get (*) text!
								for($i=0; $i<count($titles[1]); ++$i)
								{
									//print_r($titles);
									//exit();
									$name = $titletext[1][$i];
									$offset = $titles[1][$i][1];
									$level = $mark->titleLevel;
									//echo($offset." ");
									self::$headingList[$offset]=array(
										'title' => $name,
										'level' => $level);
								}
							}
						}
					}
					// NORMAL PROCESS CONTINUES

					if ($mark->process == EMU_Markup_Process::SNIPPET)
					{
						$text = preg_replace_callback($search, array($mark, 'Render'), $text);
					}
					else // NORMAL
						$text = preg_replace($search, $replace, $text);

					break;
				case EMU_Markup_Process::ALL:
					$text = $mark->Render($text);
					break;
			}
		}
		
		if (self::$generateHeadingList)
		{
			//echo "<pre>";
			ksort(self::$headingList);
			//print_r(self::$headingList);
			self::buildHeadings();
			//print_r(self::$headings);
			//echo "</pre>";
		}

		return $text;
	}

	public static function getHeadings()
	{
		return self::$headings;
	}

	private static function buildHeadings()
	{
		foreach(self::$headingList as $heading)
		{
			self::$headings[] = array(
				"level" => $heading['level'],
				"title" => $heading['title'] );
		}
	}

/*
	private static function buildHeadings()
	{
		self::recAddHeading(1, self::$headings, self::$headings, self::$headings, 0);
	}


	private static function recAddHeading($currentLevel, &$current, &$last, &$parent, $counter)
	{
		$heading = self::$headingList[$counter];
		$title = $heading['title'];
		$level = $heading['level'];

		if ($level == $currentLevel)
		{
			$newEntry = array(
				"title" => $title,
				"level" => $level,
				"content" => array() );
			$current[] = $newEntry;
			$last = &$newEntry;
			recAddHeading($level, $current, $last, $parent, ++$counter);
		}
		else if ($level > $currentLevel)
		{
			$newEntry = array(
				"title" => "",
				"level" => $currentLevel,
				"content" => array() );
			$last[]['content'] = $newEntry;
			$last = &$newEntry;
			$parent = &$current;
			$current = &$newEntry['content'];
			recAddHeading($currentLevel+1, $current, $last, $parent, ++$counter);
		}
		else if ($level < $currentLevel)
		{
			return;
		}
	}
*/

/*
	private static function buildHeadings()
	{
		if (count(self::$headingList) > 0)
		{
			$current = &self::$headings;
			$level = 1;
			$depth = 0;
			$path = array(-1);
			foreach(self::$headingList as $heading)
			{
				if ($heading['level'] < $level) // pop and come up to equal
				{
					$depth--;
					$current = &self::$headings;
					for($i=0; $i<=$depth; $i++)
					{
						$current = &$current[$path[$i]];
					}
					$level = $heading['level'];
				}

				if ($heading['level'] == $level) // save level
				{
					$newentry = array(
						"title" => $heading['title'],
						"level" => $heading['level'],
						"contents" => array()
						);
					$current[++$path[$depth]]=$newentry;
				}
				else if ($heading['level'] > $level)
				{
					if ($depth > 0)
						$current = &$current['contents'];
					else
						$current = &$current[$path[0]]['contents'];
					$depth++;
					$path[$depth]=-1;
					$newentry = array(
						"title" => $heading['title'],
						"level" => $heading['level'],
						"contents" => array()
						);
					$current[++$path[$depth]]=&$newentry;
					$level = $heading['level'];
					$depth++;
					$current = &$newentry['contents'];
				}
			}
		}
	}
*/

/* // DEPRICATED
	public static function Heading($title, $level)
	{
		$newentry = array( 
			"title" => $title, 
			"contents" => array() 
			);

		$level = $level-1; // 0 based index

		if ($level < self::$currentHeadingDepth) // pop
		{
			self::$currentHeadingDepth = $level;
			for ($i=$level+1; $i<count(self::$currentHeadingPath); ++$i)
				self::$currentHeadingPath[$i]=0;
		}

		$current = &self::$headings;

		for($i=0; $i<level; $i++)
		{
			$current = &$current[self::$currentHeadingPath[$i]];
		}

		if ($level == self::$currentHeadingPath) // push onto same level
		{
			$current[] = $newentry;
			self::$currentHeadingPath[$level]++;
		}
		else if ($level > self::$currentHeadingPath) // push onto next level (previous already taken care of above)
		{
			$current['contents'][]=$newentry;
			self::$currentHeadingPath[$level+1]=0;
			self::$currentHeadingDepth=$level;
		}
	}
*/
}




abstract class EMU_Markup_Process
{
	const NONE = 0; // internal processing
	const ALL = 1;  // pass EVERYTHING at this point to Render()
	const SNIPPET = 2; // Pass the internal snippet to Render() and just replace with what's returned
}

abstract class EMU_Markup_Regex_Type
{
	const NORMAL = 0; // Normal (default); regex is marked up within the class itself
	const PLAIN = 1; // Plain text (use preg_quote on the start and end)
}

abstract class EMU_Markup_Replace_Type
{
	const NORMAL = 0; // Normal: <start>*<end> => <newstart>*<newend>
	const SINGLE = 1; // Single: <start> => <newstart>
}

abstract class EMU_Markup
{
	var $name = "";
	var $start = "";
	var $finish = "";
	var $newStart = "";
	var $newFinish = "";

	var $isTitle = false;
	var $titleLevel = 0;

	var $process = EMU_Markup_Process::NONE;
	function Render($input) { return $input; }
	var $regexType = EMU_Markup_Regex_Type::NORMAL;
	var $replaceType = EMU_Markup_Replace_Type::NORMAL;
}

?>