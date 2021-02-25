<?php
/**
 * Copyright © 2011-2018 Karliuka Vitalii(karliuka.vitalii@gmail.com)
 *
 * See COPYING.txt for license details.
 */
namespace Faonni\SocialLogin\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;
use Faonni\SocialLogin\Model\ResourceModel\Profile as ProfileResource;

/**
 * Profile Model
 */
class Profile extends AbstractModel implements IdentityInterface
{
    /**
     * Profile cache tag
     */
    const CACHE_TAG = 'FAONNI_SOCIALLOGIN_PROFILE';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'faonni_sociallogin_profile';

    /**
     * Parameter name in event
     * In observe method you can use $observer->getEvent()->getObject() in this case
     *
     * @var string
     */
    protected $_eventObject = 'profile';

    /**
     * Model cache tag for clear cache in after save and after delete
     * When you use true - all cache will be clean
     *
     * @var string|array|bool
     */
    protected $_cacheTag = self::CACHE_TAG;

    /**
     * Initialize model
     */
    protected function _construct()
    {
        parent::_construct();

        $this->_init(ProfileResource::class);
    }

    /**
     * Retrieve Unique ID(s) for each object in system
     *
     * @return string[]
     */
    public function getIdentities()
    {
        $tags = [];
        if ($this->getId()) {
            $tags[] = self::CACHE_TAG . '_' . $this->getId();
        }
        return $tags;
    }

    /**
     * Load an Object by Multiple Fields
     *
     * @param string $fields should be ['column_name_1'=>'value', 'colum_name_2'=>'value']
     * @return Magento\Framework\Model\AbstractModel
     */
    public function loadByFields($fields)
    {
        $this->_beforeLoadByFields($fields);
        $this->_getResource()->loadByFields($this, $fields);

        $this->_afterLoad();
        $this->setOrigData();
        $this->_hasDataChanges = false;

        return $this;
    }

    /**
     * Processing Object Before Load data
     *
     * @param string $fields should be ['column_name_1'=>'value', 'colum_name_2'=>'value']
     * @return $this
     */
    protected function _beforeLoadByFields($fields = [])
    {
        $params = ['object' => $this, 'fields' => $fields];
        $this->_eventManager->dispatch('model_load_before', $params);

        $params = array_merge($params, $this->_getEventData());
        $this->_eventManager->dispatch($this->_eventPrefix . '_load_before', $params);

        return $this;
    }
}
