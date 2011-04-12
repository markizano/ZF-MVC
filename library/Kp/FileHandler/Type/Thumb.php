<?php

/**
 * Use this class to load any thumbnail picture
 *
 * @category   Ic
 * @package    Kp_FileHandler
 * @subpackage Kp_FileHandler_Type
*/
class Kp_FileHandler_Type_Thumb extends Kp_FileHandler_Type_Abstract
{
    /**
     * All needed templates for the thumbnail type
     *
     * @var array
     */

    public $templates = array(
    	'development' => array(
            'uri' => '/{{path}}',
            'filePath' => "{{appRoot}}/../htdocs/{{path}}"
        ),
    	'staging' => array(
            'uri' => '/{{path}}',
            'filePath' => "{{appRoot}}/../htdocs/{{path}}"
        ),
    	'testing' => array(
            'uri' => '/{{path}}',
            'filePath' => "{{appRoot}}/../htdocs/{{path}}"
        ),
        //prod settings roll up to all setting that do not specifically override it.
    	'production' => array(
            'fileName' => array(
                '{{name}}.{{extension}}'
            ),

    		'uri' => '/{{path}}',
    		'filePath' => array(
                '/mnt/clickboothlnk.com/www/{{path}}',
                '/mnt2/clickboothlnk.com/www/{{path}}',
                '/mnt3/clickboothlnk.com/www/{{path}}'
            )
        ),

        'defaultPath' => '{{appRoot}}/../htdocs/uploads/thumbs',
        'defaultExtension' => 'jpg'
    );
}
