<?php
// TODO: Define Kp_Acl_Collection_Abstract
abstract class Kp_Acl_Collection_Abstract extends Kp_Collection_Abstract
{
    // TODO: Define init
    public function init() {
        $this->setResourceName('Model_Acl');
        return parent::init();
    }

    // TODO: Define load
    public function load($field, $condition = array())
    {
        $this->setIsObjectNew(false);

        try {
            $stmt = $this->getResource()->query($this->_getConditionSql($field, $condition));
            $records = $this->getResource()->fetchAll(Zend_Db::FETCH_ASSOC);

            if(count($records) > 0) {
                foreach($records as $record) {
                    $acl = Ic::get('User_Acl');
                    $acl->load($record[$this->getResource()->getIdFieldName()]);

                    $acl->getUserAclGroups()->load($acl->getGroupId());
                    $acl->getUserAclGroups()->getUserAclPermissions()->load($acl->getUserAclGroups()->getId());
                    $acl->getUserAclGroups()->getUserAclPermissions()->getUserAclTempaltes()->load($$acl->getUserAclGroups()->getUserAclPermissions()->getId());
                    

                    $this->setData($acl);
                }
            
                $this->setOrigData();

                $this->flagDirty(null, false);
                $this->setDataChanges(false);
            }
        } catch(Exception $e) {
            $this->log($e);
            $this->setIsObjectNew(true);
        }

        return $this;
    }
}
