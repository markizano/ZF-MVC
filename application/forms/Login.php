<?php
/**
 *  Form_Login
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
 *  @category   ZF
 *  @package    Forms
 *  @copyright  Copyright (c) 2009-2011 Markizano Draconus <markizano@markizano.net>
 *  @license    http://framework.zend.com/license/new-bsd     New BSD License
 *  @author     Markizano Draconus <markizano@markizano.net>
 */

/**
 *  Holds the form values necessary to authenticate a user.
 *
 *  @category   ZF
 *  @package    Forms
 *  @copyright  Copyright (c) 2009-2011 Markizano Draconus <markizano@markizano.net>
 *  @license    http://framework.zend.com/license/new-bsd     New BSD License
 *  @author     Markizano Draconus <markizano@markizano.net>
 */
class Form_Login extends Zend_Form
{
    /**
     *  Holds the default username of the form.
     *  
     *  @var String
     */
    protected $_username;

    /**
     *  Adds the login form fields.
     *  
     *  @return void
     */
    public function init()
    {
        $this->addElements(array(
            'username' => new Zend_Form_Element_Text(
                'username',
                array(
                    'required'  => true,
                    'label'     => 'Username:',
                    'value'     => $this->_username,
                    'class'     => 'user-input',
                )
            ),
            new Zend_Form_Element_Password(
                'password',
                array(
                    'required'  => true,
                    'label'     => 'Password:',
                    'class'     => 'user-input',
                )
            ),
            new Zend_Form_Element_Submit(
                'submit',
                array(
                    'label'     => 'Login',
                    'class'     => 'user-submit',
                )
            )
        ));

        $this->getElement('username')
            ->addValidator(new Zend_Validate_NotEmpty());

        $this->getElement('password')
            ->addValidator(new Zend_Validate_NotEmpty());
    }

    /**
     *  Allows injection of a username into the form.
     *  
     *  @param String   $username   The username to assign.
     *  
     *  @return Form_Login
     */
    public function setUsername($username)
    {
        if (!is_null($username) && !is_string($username)) {
            throw new InvalidArgumentException('Argument 1 ($username) expected string.');
        }

        $this->_username = $username;
        return $this;
    }
}

