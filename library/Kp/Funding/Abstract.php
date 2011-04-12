<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Abstract
 *
 * @author bkozak
 */
abstract class Kp_Funding_Source_Abstrct extends Kp_Object_Abstract
{
    public function validate()
    {
        $this->_hasBeenValidated = true;
        $this->_isValid = true;
    }
}
