<?php
/**
 * Copyright © 2011-2018 Karliuka Vitalii(karliuka.vitalii@gmail.com)
 * 
 * See COPYING.txt for license details.
 */
namespace Faonni\SocialLogin\Model\ResourceModel\Provider;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\Data\Collection as AbstractCollection;
use Magento\Store\Model\ScopeInterface;

/**
 * Provider ResourceModel Collection
 */
class Collection extends AbstractCollection
{
    /**
     * Item object class name
     *
     * @var string
     */
    protected $_itemObjectClass = 'Faonni\SocialLogin\Model\Provider';
    
    /**
     * Core store config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;
    
    /**
     * Initialize collection
     *
     * @param EntityFactoryInterface $entityFactory
     * @param ScopeConfigInterface $scopeConfig     
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        ScopeConfigInterface $scopeConfig
        
    ) {
        $this->_scopeConfig = $scopeConfig;
        
		parent::__construct(
            $entityFactory
        );        
    }
    
    /**
     * Load data
     *
     * @param bool $printQuery
     * @param bool $logQuery
     * @return $this
     */
    public function loadData($printQuery = false, $logQuery = false)
    {
        if (!$this->isLoaded()) {
            /* read config */
            $config = $this->_scopeConfig
                ->getValue('faonni_socialprovider', ScopeInterface::SCOPE_STORE);
            /* add providers */
            foreach ($config as $code => $data) {
                /* create new item */
                $item = $this->getNewEmptyItem();
                $item->addData($data);
                $item->setId($code);
                /* add item to collection */
                $this->addItem($item);
            }
            usort($this->_items, array($this, 'sortOrderCollection'));
        }
        return $this->_setIsLoaded();
    }
    
	/**
	 * Sort order collection
	 *
	 * @param string $a
	 * @param string $b 
	 * @return int
	 */		
	public function sortOrderCollection($a, $b)
    {
		if (!isset($a['sort_order'], $b['sort_order']) || 
            $a['sort_order'] == $b['sort_order']
        ) {
            return 0;
        } 
		return ($a['sort_order'] < $b['sort_order']) ? -1 : 1;
    } 
    
    /**
     * Convert items array to array for select options
     *
     * return items array
     * array(
     *      $index => array(
     *          'value' => mixed
     *          'label' => mixed
     *      )
     * )
     *
     * @param string $valueField
     * @param string $labelField
     * @param array $additional
     * @return array
     */
    protected function _toOptionArray($valueField = 'id', $labelField = 'name', $additional = [])
    {
        return parent::_toOptionArray($valueField, 'title', $additional);
    }    
}
