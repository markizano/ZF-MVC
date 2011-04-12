<?php
/**
 * Kizano/Misc.php
 *
 * PHP version 5
 *
 * Namespace placeholder for functions that would normally be free-floating.
 * Copyright (C) 2010 Markizano Draconus <markizano@markizano.net>
 *
 * This class is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @category  Kizano
 * @package   Miscelaneous
 * @author    Markizano Draconus <markizano@markizano.net>
 * @license   http://www.gnu.org/licenses/gpl.html GNU Public License
 * @link      https://github.com/markizano/markizano/blob/master/includes/library/Kizano/Misc.php
 */

/**
 *	Namespace for miscelaneous functions that would normally be free-floating.
 *  @author Markizano Draconus <markizano@markizano.net>
 */
class Kizano_Misc
{
	/**
	 *	Gets a Console-printable string representation of the current backtrace.
	 *	@return		String	A Console-printable backtrace
	 */
	public static function textBacktrace($backtrace = null)
	{
	    if ($backtrace === null) {
    		$backtrace = self::backtrace();
    		array_shift($backtrace);
		}
		$result = null;
		if (count($backtrace))
			foreach ($backtrace as $back) {
				isset($back['class']) || $back['class'] = 'Static';
				isset($back['type']) || $back['type'] = '::';
				$result .= "<$back[file]:$back[line]> ".
					"$back[class]$back[type]".
					"$back[function]("
				;
				$comma = false;
				if (count($back['args']))
					foreach ($back['args'] as $args) {
						$result .= $comma? ', ': null;
						$comma || $comma = true;
						if (is_string($args)) {
							$result .= "'$args'";
						} elseif (is_numeric($args)) {
							$type = gettype($args);
							$result .= "($type) $args";
						} elseif (is_array($args)) {
						    $args = print_r($args, true);
							$result .= "(array) $args";
						} elseif (is_object($args)) {
							$type = gettype($args);
							if (is_callable(array($args, '__toString'))) {
								$args = $args->__toString();
							} else{
								$args = get_class($args);
							}
							$result .= "($type) $args";
						} elseif (is_bool($args)) {
							$args = $args? 'true': 'false';
							$result .= "(boolean) $args";
						} elseif (is_null($args)) {
							$result .= "null";
						} else{
							$type = gettype($args);
							$result .= "($type) [object]";
						}
					}
				$result .= ");\n";
			}
		return $result;
	}

	/**
	 *	Gets a Console-printable string representation of the current backtrace.
	 *	@return		String	A Console-printable backtrace
	 */
	public static function consoleBacktrace($backtrace = null)
	{
	    defined('STDOUT') || define('STDOUT', fOpen('php://stdout', 'a'));
	    if ($backtrace === null) {
    		$backtrace = self::backtrace();
    		array_shift($backtrace);
		}
		$result = null;
		if (count($backtrace))
			foreach ($backtrace as $back) {
				isset($back['class']) || $back['class'] = 'Static';
				isset($back['type']) || $back['type'] = '::';
				$result .= "<\033[31m$back[file]\033[00m:\033[01;30m$back[line]\033[00;00m> ".
					"\033[34m$back[class]\033[00m$back[type]".
					"\033[34m$back[function]\033[00m("
				;
				$comma = false;
				if (count($back['args']))
					foreach ($back['args'] as $args) {
						$result .= $comma? ', ': null;
						$comma || $comma = true;
						if (is_string($args)) {
							$result .= "\033[31m'$args'\033[00m";
						} elseif (is_numeric($args)) {
							$type = gettype($args);
							$result .= "(\033[32m$type\033[00m) $args";
						} elseif (is_array($args)) {
						    $args = print_r($args, true);
							$result .= "(\033[32marray\033[00m) $args";
						} elseif (is_object($args)) {
							$type = gettype($args);
							if (is_callable(array($args, '__toString'))) {
								$args = $args->__toString();
							} else{
								$args = get_class($args);
							}
							$result .= "(\033[32m$type\033[00m) $args";
						} elseif (is_bool($args)) {
							$args = $args? 'true': 'false';
							$result .= "(\033[32mboolean\033[00m) $args";
						} elseif (is_null($args)) {
							$result .= "\033[31mnull\033[00m";
						} else{
							$type = gettype($args);
							$result .= "(\033[32m$type\033[00m) [object]";
						}
					}
				$result .= ");<br />\n";
			}
		return $result;
	}

	/**
	 *	Gets a HTML-printable string representation of the current backtrace.
	 *	@return		String	A HTML-printable backtrace
	 */
	public static function htmlBacktrace($backtrace = null)
	{
	    if ($backtrace === null) {
    		$backtrace = self::backtrace();
    		array_shift($backtrace);
		}
		$result = null;
		if (count($backtrace))
			foreach ($backtrace as $back) {
				isset($back['class']) || $back['class'] = 'Static';
				isset($back['type']) || $back['type'] = '::';
				$result .= "&lt;<span style='color:#CC0000;'>$back[file]</span>:$back[line]&gt;&nbsp;".
					"<span style='color:#0000AA;'>$back[class]</span>$back[type]".
					"<span style='color:#0000AA;'>$back[function]</span>("
				;
				$comma = false;
				if (count($back['args']))
					foreach ($back['args'] as $args) {
						$result .= $comma? ', ': null;
						$comma || $comma = true;
						if (is_string($args)) {
							$result .= "<span style='color:#CC0000;'>'$args'</span>";
						} elseif (is_numeric($args)) {
							$type = gettype($args);
							$result .= "(<span style='color:#00CC00;'>$type</span>) $args";
						} elseif (is_array($args)) {
							$type = gettype($args);
							$args = print_r($args, true);
							$result .= "(<span style='color:#00CC00;'>$type</span>) $args";
						} elseif (is_object($args)) {
							$type = gettype($args);
							if (is_callable(array($args, '__toString'))) {
								$args = $args->__toString();
							} else{
								$args = get_class($args);
							}
							$result .= "(<span style='color:#00CC00;'>$type</span>) $args";
						} elseif (is_bool($args)) {
							$args = $args? 'true': 'false';
							$result .= "(<span style='color:#00CC00;'>boolean</span>) $args";
						} elseif (is_null($args)) {
							$result .= "<span style='color:#CC0000;'>null</span>";
						} else{
							$type = gettype($args);
							$result .= "(<span style='color:#00CC00;'>$type</span>) [object]";
						}
					}
				$result .= ");<br />\n";
			}
		return $result;
	}

	/**
	 * Returns a custom-created backtrace. One that doesn't include the dumping of irrelevant objects.
	 * @return	 Array   The [corrected] backtrace
	 */
	public static function backtrace()
	{
		$debug = debug_backtrace();
		if (defined('STDOUT')) {
		    $prefix = "\033[32m";
		    $suffix = "\033[00m";
		} else {
		    $prefix = "<span style='color:#00CC00;'>";
		    $suffix = "</span>";
		}
		array_shift($debug);
		foreach ($debug as $i => $deb) {
			unset($debug[$i]['object']);
			foreach ($deb['args'] as $k => $d) {
				is_object($d) && $debug[$i]['args'][$k] = "({$prefix}object{$suffix})".get_class($d);
				if (is_array($d)) {
				    $debug[$i]['args'][$k] = "({$prefix}array{$prefix})\n[\n\t";
				    foreach ($d as $key => $val) {
    				    $args[] = sprintf("\t$key => ({$prefix}%s{$suffix}) %s", getType($val), is_string($val)? $val: null);
				    }
				    $debug[$i]['args'][$k] .= join(",\n", $args)."\n]";
			    }
			}
		}
		return $debug;
	}
}
