<?php
// TODO: define Kp_User_Abstract
class Kp_User_Abstract extends Kp_Object_Abstract
{
    // TODO: define init
    public function init() {
        $this->setResourceName('Model_User');
        return parent::init();
    }

    public function hashPassword($password)
    {
        $options = Zend_Registry::get('application')->getOptions();

        if(!isset($options['password_hash_key'])) {
            $options['password_hash_key'] = 'th1s_is_th3_hash_ke3y_859';
            Zend_Registry::get('application')->setOptions($options);
        }

        return md5($password . $options['password_hash_key']);
    }

    public function validate($password)
    {
        $valid = $this->_validate($password);

        $UserActivity = Ic::get('User_Activity');

        $ip = $_SERVER["REMOTE_ADDR"];
        $time = date("Y-m-d G:i:s", time());
        $account_user_id = $performed_by_user_id = $this->getId();

        $data = array(
                'account_user_id'       => $account_user_id
                ,'performed_by_user_id' => $performed_by_user_id
                ,'type_code'            => null
                ,'old_data'             => ''
                ,'new_data'             => ''
                ,'user_ip_address'      => $ip
                ,'timestamp'            => $time
        );
        
        if(!$valid) {
            $data['type_code'] = $UserActivity->LOG_TYPE_CODE_FAILED_LOGIN;

            $UserActivity->setData($data);
            $activitySaved = $UserActivity->save();

            return false;
        }
        
        $data['type_code'] = $UserActivity->LOG_TYPE_CODE_LOGIN;
        $UserActivity->setData($data);
        $activitySaved = $UserActivity->save();

        return true;
    }

    protected function _validate($password)
    {
        if(!$this->getId()) {
            Zend_Registry::get('Message_Que')->build(
                array(
                    'message'       => "We can't seem to find a use with that name. Please make sure your caps lock key is turned off."
                    ,'message_type' => Kp_Message::$ERR
                )
             );
            return false;
        }

        $postedPassHash = $this->hashPassword($password);
        if(!$this->getPasswordIsHashed()) {
            if($password == $this->getPassword()) {
                $this->setPassword($postedPassHash);
                $this->setPasswordIsHashed(1);
                $saved = $this->save();

                if(!$saved) {
                     Zend_Registry::get('Message_Que')->build(
                        array(
                            'message'       => "There has been an issues comminicating with our system. Please try again later."
                            ,'message_type' => Kp_Message::$ERR
                        )
                     );
                     return false;
                }
            } else {
                Zend_Registry::get('Message_Que')->build(
                    array(
                        'message'       => "Incorrect username or password.1"
                        ,'message_type' => Kp_Message::$ERR
                    )
                );
                return false;
            }
        }

        if($postedPassHash == $this->getPassword()) {
            if($this->getUseok() == 2) {
                echo("==2");
                $lock_time = -1;
                if($this->getUseokLastModifiedTime() != "") {
                    $lock_time = strtotime($this->getUseokLastModifiedTime());
                }
                $allowed_time = time() - (60 * 30);  //Expiration of 30 minutes

                if ($lock_time == -1 || $lock_time > $allowed_time) {
                    Zend_Registry::get('Message_Que')->build(
                        array(
                            'message'       => "This account has been locked due to too many login failures.  Please complete the 'forgot password' process or contact the system administrator."
                            ,'message_type' => Kp_Message::$ERR
                        )
                    );

                    return false;
                } else {
                    $this->setUseok(0);
                    $this->setUseokLastModifiedTime(date("Y-m-d G:i:s", time()));

                    $save = $this->save();

                    if(!$saved) {
                        Zend_Registry::get('Message_Que')->build(
                            array(
                                'message'       => "There has been an issues comminicating with our system. Please try again later."
                                ,'message_type' => Kp_Message::$ERR
                            )
                        );
                        return false;
                    }
                }
            }

            if($this->getStatus() == "9") {
                Zend_Registry::get('Message_Que')->build(
                    array(
                        'message'       => "Account closed.  Please contact the network administrator for assistance."
                        ,'message_type' => Kp_Message::$ERR
                    )
                );
                return false;
            }

            if($this->getType() != 'adm') {
                $start_date = time() - (60 * 60 * 30);

                $conditionsKeys['id']           = 'performed_by_user_id';
                $conditionsKeys['starttime']    = 'timestamp';

                $conditions['id']        = array("eq"   => $this->getId());
                $conditions['starttime'] = array("gteq" => $start_date);

                $userActivity = $this->getUserActivity($conditionsKeys, $conditions);

                $threshold_exceeded = false;
                $failure_count = 0;
                $done = false;

                if(!$userActivity->isCollection()) {
                    $userActivity = Ic::get('User_Activity_Collection')->setData($userActivity->getId(), $userActivity);
                }

                foreach($userActivity as $activity) {
                    if($done) {
                        break;
                    }

                    if($activity->getTypeCode() == $activity->LOG_TYPE_CODE_PASSWORD_RESET) {
                        if($failure_count <= 1) {
                            Zend_Registry::get('Message_Que')->build(
                                array(
                                    'message'       => "The provided credentials did not match those sent by email.  Another failure will result in the account requiring another password reset."
                                    ,'message_type' => Kp_Message::$ERR
                                )
                            );
                            return false;
                        } else {
                            $threshold_exceeded = true;
                            break;
                        }
                    } elseif($activity->getTypeCode() == $activity->LOG_TYPE_CODE_FAILED_LOGIN) {
                        $failure_count++;
                        if($failure_count >= 10) {
                            $threshold_exceeded = true;
                            break;
                        }
                    } elseif($activity->getTypeCode() == $activity->LOG_TYPE_CODE_LOGIN) {
                        $done = true;
                    }
                }

                if($threshold_exceeded) {
                    $this->setUseok(2);
                    $this->setUseokLastModifiedTime(time());

                    $saved = $this->save();

                    if (!$saved) {
                        Zend_Registry::get('Message_Que')->build(
                            array(
                                'message'       => "There has been an issues comminicating with our system. Please try again later."
                                ,'message_type' => Kp_Message::$ERR
                            )
                        );
                        return false;
                    }
                    return false;
                }//end if threshold exceeded

                return true;
            } else {
                return true;
            }
        }
        
        Zend_Registry::get('Message_Que')->build(
            array(
                'message'       => "Incorrect username or password."
                ,'message_type' => Kp_Message::$ERR
            )
        );
        
        return false;
    }
}
