<?php
/**
 * Copyright © 2011-2018 Karliuka Vitalii(karliuka.vitalii@gmail.com)
 * 
 * See COPYING.txt for license details.
 */
namespace Faonni\SocialLogin\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\AbstractModel;

/**
 * Profile ResourceModel
 */
class Profile extends AbstractDb
{
    /**
     * Initialize model
     */
    protected function _construct()
    {
        $this->_init('faonni_sociallogin_profile', 'profile_id');
    }
    
    /**
     * Load an Object by Multiple Fields
	 *
     * @param AbstractModel $object
     * @param string $fields should be ['column_name_1'=>'value', 'colum_name_2'=>'value']
     * @return $this
    */	
	public function loadByFields(AbstractModel $object, $fields=[])
    {
        $connection = $this->getConnection();
        
        if ($connection && is_array($fields)) {
            $select = $this->_getLoadByFieldsSelect($fields, $object);
            $data = $connection->fetchRow($select);

            if ($data) {
                $object->setData($data);
            }
        }
        $this->unserializeFields($object);
        $this->_afterLoad($object);

        return $this;        
    }	
        
    /**
     * Retrieve Select Object for load by Fields Object data
     *
     * @param array $fields
     * @param AbstractModel $object
     * @return \Magento\Framework\DB\Select
     */
    protected function _getLoadByFieldsSelect($fields=[], $object)
    {
        $select = $this->getConnection()
			->select()
			->from($this->getMainTable());
        
		foreach ($fields as $field => $value) {
			$field = $this->getConnection()->quoteIdentifier(
				sprintf('%s.%s', $this->getMainTable(), $field)
			);
			$select->where($field . '=?', $value);
		} 
		
        return $select;
    }    
}
