<?php
/**
 * Kizano_Service_Lipsum
 *
 * PHP version 5.*
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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * @category Kizano
 * @package Kizano
 * @author Markizano Draconus <markizano@markizano.net>
 * @copyright 2010 Markizano Draconus
 * @license http://www.gnu.org/licenses/gpl.html GNU Public License
 * @link https://github.com/markizano/markizano/blob/master/includes/library/Kizano/Service/Lipsum.php
 */

/**
 * Interface for generating lipsums on the fly
 *
 * @category   Kizano
 * @package    Kizano_Service
 */
class Kizano_Service_Lipsum
{
    /**
     *  The location where we should post the service.
     */
    const FEED_URL = 'http://lipsum.com/feed/xml/?amount=%d&what=%s&start=%s';

    /**
     *  The valid values for the "what" parameter.
     *  
     *  @var Array
     */
    public static $types = array(
        'paras',
        'words',
        'bytes',
        'lists',
    );

    /**
     *  Grabs a lipsum from the lipsum site.
     *  
     *  @param $amount      Integer     The number of items to grab.
     *  @param $what        enum        What to grab from the site. Can be one of self::$types
     *  @param $start       boolean     Should the lipsum start with "Lorem ipsum..."
     */
    public static function getLipsum($amount, $what, $start = false)
    {
        $url = sprintf(self::FEED_URL, $amount, $what, (bool)$start? 'yes': 'no');
        $client = new Zend_Http_Client($url);
        $xml = new DomDocument(1.0, 'utf-8');
        $xml->loadXML($client->request()->getBody());
        $lipsum = $xml->getElementsByTagName('feed')->item(0)->getElementsByTagName('lipsum')->item(0)->nodeValue;
        return $lipsum;
    }
}

