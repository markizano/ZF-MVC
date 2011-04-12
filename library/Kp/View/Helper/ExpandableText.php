<?php
/**
 * @category   Ic
 * @package    Kp_View
 * @subpackage Helper
 * @version    $Id$
 */

/**
 * Helper for making thumbnail img tags
 *
 * @package    Kp_View
 * @subpackage Helper
 */
class Kp_View_Helper_ExpandableText extends Zend_View_Helper_Abstract
{
    var $chars = 255;
    var $id = null;
    var $class = 'expandable_text';
    var $linkText = 'More';
    var $revertText = 'Minimize';
    
    /**
     * Chops text to fit into a minimized display area.  Expands it, can reminimize it.  Note, this obj is only called 
     * once per page, so if change settings, it's going to change for all following items, unless override the opts
     * @access public
     *
     * @return string text output and js
    */
    public function ExpandableText($text, $opts=array() )
    {
       //set values:
       $r = rand(2500, 99000000); 
       $this->id = ( isset($opts['id']) ? $opts['id'] : 'exp_text_' . $r);

       if( isset($opts['chars']) ) {
           $this->chars = $opts['chars'];
       }
       
       if( isset($opts['linkText']) ) {
           $this->linkText = $opts['linkText'];
       }
       
       if( isset($opts['revertText']) ) {
           $this->revertText = $opts['revertText'];
       }
       
       if( isset($opts['class']) ) {
           $this->class .= ' ' . $opts['class'];
       }
       
       //give an option to revert back to chopped, unless they said not to via opts:
       $revert = '';
       if ( $opts['ignoreRevert'] != 'T') {
           $revert = " <span class='revert_to_chopped_link' id='revert_text_{$r}'>{$this->revertText}</span>";
       }
       
       //cut the string length:
       $txt = $this->cutClean($text, $this->chars);
       $out = "<div id='{$this->id}' class='{$this->class}'>";
       $out .= $txt;
       $out .= "</div>";
       $arr = array('full'=>$text . $revert, 'chopped'=>$txt);
       $out .= "<script type='text/javascript'>var {$this->id} = ".json_encode($arr).";</script>";
       return $out;
    }

    /*
     * we do not want to just CUT the text, we want to cut it clean, 
     * find the last SPACE before the max length.
     */
    private function cutClean($text, $chars)
    {
        if( strlen($text) > $chars) { 
            $text = substr($text, 0, $chars);
            for($i=1; $i < $chars; $i++)
            {
                $c = substr($text, -$i, 1);
                if($c == ' ' || $c == '.' || $c == '?' || $c == '!')
                {
                    $text = substr($text, 0, ($chars-$i) );
                    break;
                }
            }
            $text = trim($text) . '... ';
            $lnk = "<span class='expandable_text_link'>{$this->linkText}</span>";  
            $text = $text . $lnk;  
        }
        return $text; 
    }
}    //end class