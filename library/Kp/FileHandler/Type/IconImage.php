<?php

/**
 * Use this class to load any IconImage
 *
 * @category Ic
 * @package  Kp_FileHandler
 * @subpackage Kp_FileHandler_Type
 * @author Xeon Xai <xeon@integraclick.com>
 */
class Kp_FileHandler_Type_IconImage extends Kp_FileHandler_Type_Abstract
{
    /**
     * All needed templates for the IconImage type
     *
     * @var array
     */
    public $templates = array(
        'development' => array(
            'uri' => '/uploads/icons/{{type}}/{{firstOfIconId}}',
            'filePath' => '{{appRoot}}/../htdocs/uploads/icons/{{type}}/{{firstOfIconId}}'
        ),
        'staging' => array(
            'uri' => '/uploads/icons/{{type}}/{{firstOfIconId}}',
            'filePath' => '{{appRoot}}/../htdocs/uploads/icons/{{type}}/{{firstOfIconId}}'
        ),
        'testing' => array(
            'uri' => '/uploads/icons/{{type}}/{{firstOfIconId}}',
            'filePath' => '{{appRoot}}/../htdocs/uploads/icons/{{type}}/{{firstOfIconId}}'
        ),
        'production' => array(
            'fileName' => array(
                '{{iconId}}.{{fileExtension}}'
            ),
            'defaultUri'       => '/images/icon/{{type}}',
            'defaultPath'      => '{{appRoot}}/../htdocs/images/{{type}}',
            'defaultExtension' => 'gif',
            'defaultFile'      => 'default.gif'
        )
    );
}