<?php
/**
 *  Kizano_Application_Resource_Acl
 *
 *  LICENSE
 *
 *  This source file is subject to the new BSD license that is bundled
 *  with this package in the file LICENSE.txt.
 *  It is also available through the world-wide-web at this URL:
 *  http://framework.zend.com/license/new-bsd
 *  If you did not receive a copy of the license and are unable to
 *  obtain it through the world-wide-web, please send an email
 *  to license@zend.com so we can send you a copy immediately.
 *
 *  @category   Kizano
 *  @package    Application
 *  @copyright  Copyright (c) 2009-2011 Markizano Draconus <markizano@markizano.net>
 *  @license    http://framework.zend.com/license/new-bsd     New BSD License
 *  @author     Markizano Draconus <markizano@markizano.net>
 */

/**
 *  ACL manipulation resource object. Creates an instance of the ACL and stores
 *  it in the registry.
 *
 *  @category   Kizano
 *  @package    Application
 *  @copyright  Copyright (c) 2009-2011 Markizano Draconus <markizano@markizano.net>
 *  @license    http://framework.zend.com/license/new-bsd     New BSD License
 *  @author     Markizano Draconus <markizano@markizano.net>
 */
class Kizano_Application_Resource_Acl extends Zend_Application_Resource_ResourceAbstract
{
    /**
     *    Starts up the initializes the ACL
     *    
     *    @return Kizano_Acl
     */
    public function init()
    {
        $this->_bootstrap->bootstrap('moduleConfig');

        $acl = new Kizano_Acl($this->getOptions());
        $acl->setupRoles();
        $acl->setupDefaults();

        $aclHelper = new Kizano_Controller_Helper_Acl($acl);

        Zend_Controller_Action_HelperBroker::addHelper($aclHelper);
        Zend_Registry::set('acl', $acl);

        return $acl;
    }
}

