<?php

/**
 * Use this class to load any creative
 * 
 * @category Ic
 * @package  Kp_FileHandler
 * @subpackage Kp_FileHandler_Type
 * @author James Solomon <james@integraclick.com>, Brandon Kozak <brandon@integraclick.com>
 */
class Kp_FileHandler_Type_CreativePreview extends Kp_FileHandler_Type_Abstract
{
    /**
     * All needed templates for the Creative type
     * 
     * @var array
     */
    public $templates = array(
        'development' => array(
            'uri' => '/uploads/creative-preview.{{timestamp}}/{{creativeId}}',
            'filePath' => '{{appRoot}}/../htdocs/uploads/creative-preview/{{creativeId}}'
        ),
        'staging' => array(
            'uri' => '/uploads/creative-preview.{{timestamp}}/{{creativeId}}',
            'filePath' => '{{appRoot}}/../htdocs/uploads/creative-preview/{{creativeId}}'
        ),
        'testing' => array(
            'uri' => '/uploads/creative-preview.{{timestamp}}/{{creativeId}}',
            'filePath' => '{{appRoot}}/../htdocs/uploads/creative-preview/{{creativeId}}'
        ),
        'production' => array(
            'fileName'         => '{{previewName}}.png',
            'defaultUri'       => '/images/creative',
            'defaultPath'      => '{{appRoot}}/../htdocs/images/creative',
            'defaultExtension' => 'gif',
            'defaultFile'      => 'creative-preview.{{fileExtension}}',
            'uri'              => array('http://servedby.clickboothlnk.com/{{firstOfCampaignId}}/{{campaignId}}'),
            'filePath'         => array(
                '/mnt/clickboothlnk.com/www/creative-preview/{{creativeId}}',
                '/mnt2/clickboothlnk.com/www/creative-preview/{{creativeId}}',
                '/mnt3/clickboothlnk.com/www/creative-preview/{{creativeId}}'
            )
        )
    );
    
    /**
     * This will take all provided settings and the templates
     * and build the URI for loading the file
     * 
     * @overload
     * @return string Generated URI
     */
    public function buildUri($checkPath = true)
    {
        $file = $this->buildPath();
        $fileLastModified = filemtime($file);
        $this->setOption("timestamp", $fileLastModified);
        
        return parent::buildUri($checkPath);
    }
}