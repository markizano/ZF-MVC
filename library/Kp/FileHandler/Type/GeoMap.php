<?php

/**
 * Use this class to load any thumbnail picture
 *
 * @category   Ic
 * @package    Kp_FileHandler
 * @subpackage Kp_FileHandler_Type
*/
class Kp_FileHandler_Type_GeoMap extends Kp_FileHandler_Type_Abstract
{
    /**
     * All needed templates for the thumbnail type
     *
     * @var array
     */

    public $templates = array(
    	'development' => array(
            'uri' => '/uploads/geoMaps',
            'filePath' => "{{appRoot}}/../htdocs/uploads/geoMaps"
        ),
    	'staging' => array(
            'uri' => '/uploads/geoMaps',
            'filePath' => "{{appRoot}}/../htdocs/uploads/geoMaps"
        ),
    	'testing' => array(
            'uri' => '/uploads/geoMaps',
            'filePath' => "{{appRoot}}/../htdocs/uploads/geoMaps"
        ),
        //prod settings roll up to all setting that do not specifically override it.
    	'production' => array(
            'fileName' => array(
                '{{name}}.{{extension}}'
            ),

    		'uri' => '/uploads/geoMaps',
        		'filePath' => array(
                    '/mnt/clickboothlnk.com/www/uploads/geoMaps',
                    '/mnt2/clickboothlnk.com/www/uploads/geoMaps',
                    '/mnt3/clickboothlnk.com/www/uploads/geoMaps'
                )
            ),

            'defaultUri' => '/uploads/geoMaps',
            'defaultPath' => '{{appRoot}}/../htdocs/uploads/geoMaps',
            'defaultExtension' => 'png',
    );
}