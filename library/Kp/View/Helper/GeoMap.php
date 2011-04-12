<?php
/**
 * @category   Ic
 * @package    Kp_View
 * @subpackage Helper
 * @version    $Id$
 * 
 * Helper for making thumbnail img tags
 *
 * @package    Kp_View
 * @subpackage Helper
 */
class Kp_View_Helper_GeoMap extends Zend_View_Helper_Abstract
{
    var $w = 150;
    var $h = 80;

    /**
     * Generates a google chart img with the supplied information.
     * when building a map, pass in array of countries to method.
     * use that to build a unique name, and md5 it.  Check our geo directory for the img.  If it exists, set the name and use a viewHelper to show it.
     * if it does not exist, create it, and return that name for the viewHelper to use same as above.
     * just pass in an array ($arr) of country codes
     * @access public
     *
     * @return string tag for the img
    */
    public function GeoMap($inc=array(), $exc=array(), $opts=array())
    {
        if (!is_array($inc))
            $inc = array();

        //set values:
        $this->h = (isset($opts['h']) ? $opts['h'] : $this->h);
        $this->w = (isset($opts['w']) ? $opts['w'] : $this->w);

        //uppercase is important to google:
        $inc = array_map('strtoupper', $inc);
        $exc = array_map('strtoupper', $exc);

        //build name
        asort($inc);
        asort($exc);

        $incName = join('', $inc);
        $excName = join('', $exc);

        $fileName = ($incName != '' ? 'inc_' . $incName : '');
        if($excName != '') {
            if($fileName != '') {
                $fileName .= '_';
            }
            $fileName .= 'exc_' . $excName;
            $fileName .= '_' . $this->w . 'x' . $this->h;
        }

        if($fileName == '') {
            $fileName = 'default';
        } else {
            $fileName .= '_'.$this->h.'_'.$this->w;    //add in h & w to file name
            $fileName = md5($fileName);
        }

        //check to see if it exists:
        $file = array();
        $file['name'] = $fileName;
        $file['extension'] = 'png';
        $fileClass = Kp_FileHandler::factory('geoMap', $file);
        $imageDirPath = $fileClass->buildPath();

        if (!$imageDirPath) {
            //build values for google:
            $points = array();
            if ($inc)
                $points += array_fill(0, count($inc), '100');

            if ($exc)
                $points += array_fill(0, count($exc), '0');

            $disVals .= join(',', $points);

            //make url for google chart:
            $url  = 'http://chart.apis.google.com/chart?';
            $url .= "cht=t&chtm=world&chs={$this->w}x{$this->h}&chco=DBDBDB,FF0000,0000FF";
            $url .= "&chld={$incName}{$excName}&chd=t:{$disVals}&chf=bg,s,F8F9FB";

            if ($data = $fileClass->getFileContents($url)) {
                $fileClass->saveFileContents($data);
                $imageDirPath = $fileClass->buildPath();
            }

            //now, if somehow we failed to create it... show default:
            if (!$imageDirPath) {
                $file['name'] = 'default';
                $fileClass = Kp_FileHandler::factory('geoMap', $file);
                $imageDirPath = $fileClass->buildPath();
            }
        }

        $imagePath = $fileClass->buildUri();
        return "<img src='{$imagePath}' border='0' />";
    }
}
