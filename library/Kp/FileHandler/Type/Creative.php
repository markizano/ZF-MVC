<?php

/**
 * Use this class to load any creative
 * 
 * @category Ic
 * @package  Kp_FileHandler
 * @subpackage Kp_FileHandler_Type
 * @author James Solomon <james@integraclick.com>, Brandon Kozak <brandon@integraclick.com>
 */
class Kp_FileHandler_Type_Creative extends Kp_FileHandler_Type_Abstract
{
    /**
     * All needed templates for the Creative type
     * 
     * @var array
     */
    public $templates = array(
        'development' => array(
            'uri' => '/uploads/creatives/{{firstOfCampaignId}}/{{campaignId}}',
            'filePath' => '{{appRoot}}/../htdocs/uploads/creatives/{{firstOfCampaignId}}/{{campaignId}}'
        ),
        'staging' => array(
            'uri' => '/uploads/creatives/{{firstOfCampaignId}}/{{campaignId}}',
            'filePath' => '{{appRoot}}/../htdocs/uploads/creatives/{{firstOfCampaignId}}/{{campaignId}}'
        ),
        'testing' => array(
            'uri' => '/uploads/creatives/{{firstOfCampaignId}}/{{campaignId}}',
            'filePath' => '{{appRoot}}/../htdocs/uploads/creatives/{{firstOfCampaignId}}/{{campaignId}}'
        ),
        'production' => array(
            'fileName' => array(
                //'{{creativeId}}-b.{{fileExtension}}',
                '{{creativeId}}-ub.{{fileExtension}}'
            ),
            'defaultUri'       => '/images/creative',
            'defaultPath'      => '{{appRoot}}/../htdocs/images/creative',
            'defaultExtension' => 'gif',
            'defaultFile' => 'creative-preview.{{fileExtension}}',
            'uri' => array('http://servedby.clickboothlnk.com/{{firstOfCampaignId}}/{{campaignId}}'),
            'filePath' => array(
                '/mnt/clickboothlnk.com/www/{{firstOfCampaignId}}/{{campaignId}}',
                '/mnt2/clickboothlnk.com/www/{{firstOfCampaignId}}/{{campaignId}}',
                '/mnt3/clickboothlnk.com/www/{{firstOfCampaignId}}/{{campaignId}}'
            )
        )
    );
}