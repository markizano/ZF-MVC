<?php

/**
 * Use this class to load any account Profile picture
 *
 * @category   Ic
 * @package    Kp_FileHandler
 * @subpackage Kp_FileHandler_Type
 * @author     James Solomon <james@integraclick.com>
 * @author     Xeon Xai      <xeon@integraclick.com>
 */
class Kp_FileHandler_Type_ProfilePic extends Kp_FileHandler_Type_Abstract
{
    /**
     * All needed templates for the Profile type
     *
     * @var array
     */
    public $templates = array(
        'development' => array(
            'uri' => '/uploads/accounts/{{firstOfaccountId}}/{{accountId}}',
            'filePath' => '{{appRoot}}/../htdocs/uploads/accounts/{{firstOfaccountId}}/{{accountId}}',

            'defaultUri' => '/images/demo/profile',
            'defaultPath' => '{{appRoot}}/../htdocs/images/demo/profile',
        ),
        'staging' => array(
            'uri' => '/uploads/accounts/{{firstOfaccountId}}/{{accountId}}',
            'filePath' => '{{appRoot}}/../htdocs/uploads/accounts/{{firstOfaccountId}}/{{accountId}}'
        ),
        'testing' => array(
            'uri' => '/uploads/accounts/{{firstOfaccountId}}/{{accountId}}',
            'filePath' => '{{appRoot}}/../htdocs/uploads/accounts/{{firstOfaccountId}}/{{accountId}}'
        ),
        'production' => array(
            'fileName' => array(
                '{{name}}.{{fileExtension}}'
            ),
            'defaultUri' => '/images/profile/defaults',
            'defaultPath' => '{{appRoot}}/../htdocs/images/profile/defaults',
            'defaultExtension' => 'png',
            'defaultFile' => 'profile.{{fileExtension}}'
        )
    );

    public function __construct($options = array())
    {
        parent::__construct($options);

        $accountId = $this->getOption('accountId');
        $this->setOption('firstOfaccountId', $accountId[0]);

        if (!$this->getOption('name'))
            $this->setOption('name', 'profile');

        if (AE != 'production')
            $this->templates['production']['defaultFile'] = (($accountId % 10) + 1) . '.jpeg';
    }
}