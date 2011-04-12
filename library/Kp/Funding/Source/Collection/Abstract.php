<?php
class Kp_Funding_Source_Collection_Abstract extends Kp_Collection_Abstract
{
    protected function tryConvert($obj)
    {
        if($obj->current()->hasData('type')) {
            $type = $obj->current()->getType();

            $type = $this->_underscore($type);
            $type = ucfirst($type);

            $class = 'Funding_Source_'.$type."_Collection";

            $classExists = Ic::exists($class, true);
            if(!empty($classExists)) {
                $obj = Ic::get($class);
                $obj->setCollection($obj->getCollection());
                $obj->flagDirty(null, false, true);
            }
        }

        return $obj;
    }
}