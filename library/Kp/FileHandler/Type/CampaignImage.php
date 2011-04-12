<?php

/**
 * Use this class to load any CampaignImage
 * 
 * @category Ic
 * @package  Kp_FileHandler
 * @subpackage Kp_FileHandler_Type
 * @author James Solomon <james@integraclick.com>, Brandon Kozak <brandon@integraclick.com>
 */
class Kp_FileHandler_Type_CampaignImage extends Kp_FileHandler_Type_Abstract
{
    /**
     * All needed templates for the CampaignImage type
     * 
     * @var array
     */
    public $templates = array(
        'development' => array(
            'uri' => '/uploads/campaigns/{{firstOfCampaignId}}/{{campaignId}}',
            'filePath' => '{{appRoot}}/../htdocs/uploads/campaigns/{{firstOfCampaignId}}/{{campaignId}}'
        ),
        'staging' => array(
            'uri' => '/uploads/campaigns/{{firstOfCampaignId}}/{{campaignId}}',
            'filePath' => '{{appRoot}}/../htdocs/uploads/campaigns/{{firstOfCampaignId}}/{{campaignId}}'
        ),
        'testing' => array(
            'uri' => '/uploads/campaigns/{{firstOfCampaignId}}/{{campaignId}}',
            'filePath' => '{{appRoot}}/../htdocs/uploads/campaigns/{{firstOfCampaignId}}/{{campaignId}}'
        ),
        'production' => array(
            'fileName' => array(
                '{{name}}.{{fileExtension}}'
            ),
            'timestamp'        => true,
            'defaultUri'       => '/images/campaign',
            'defaultPath'      => '{{appRoot}}/../htdocs/images/campaign',
            'defaultExtension' => 'gif',
            'defaultFile'      => '{{name}}.{{fileExtension}}'
        )
    );
}