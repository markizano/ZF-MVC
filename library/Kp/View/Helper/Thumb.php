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
class Kp_View_Helper_Thumb extends Zend_View_Helper_Abstract
{
    public $defaultH = 120;
    public $defaultW = 120;

    /**
     * Generates <img> tag witht he supplied information.
     * Scales the img to whatever passed in opts for h / w, this normally means a thumbnail, but does not have to be a reducer,
     * it can enlarge the img in the correct proportions as well.  that loses some quality, of course.
     *
     * @access public
     * @param array $opts list or options, like img extension, height, width, an array.
     * @param array $attrs list of img tag attributes, like border height, width, etc.
     *
     * @return string tag for the img
     */
    public function thumb($name, $path, $opts=array(), $attrs=array())
    {
        $file = array(
            'path'      => $path,
            'name'      => $name,
            'extension' => ($opts['ext'] == '' ? 'jpg' : $opts['ext']),
        );
        $fileClass = Kp_FileHandler::factory('thumb', $file);
        $imagePath = $fileClass->buildUri();

        //if passed in height in width in ATTRS, then we ignore opts for H & W, otherwise we need to set it.
        //and, the default thumbnail size is 120x120 if it wasn't set in the OPTS array.
        if(!isset($attrs['height']) && !isset($attrs['width'])) {
            $imgInfo = getimagesize($fileClass->buildPath());
            $rawW = $imgInfo[0];
            $rawH = $imgInfo[1];

            $h = ($opts['h'] == '' ? $this->defaultH : $opts['h']);
            $w = ($opts['w'] == '' ? $this->defaultW : $opts['w']);

            // if the actual img size is != our preferred size, we gotta refactor it to the smaller of the two dimensions,
            // and keep the proportions correct.
            if($rawH != $h || $rawW != $w) {
                $hFactor = $rawH / $h;
                $wFactor = $rawW / $w;
                if ($hFactor > $wFactor) {
                    $w = floor($rawW / $hFactor);
                } else {
                    $h = floor($rawH / $wFactor);
                }
            }
            if($w >= 1)
                $attrs['width'] = $w;

            if($h >= 1)
                $attrs['height'] = $h;
        }

        $attrTags = '';
        if (!empty($attrs)) {
            $attrParts = array();
            foreach ($attrs as $k => $v) {
                $attrParts[] = $k . "='" . $v . "'";
            }
            $attrTags = implode(' ', $attrParts);
        }

        return "<img src='{$imagePath}' $attrTags />";
    }
}
