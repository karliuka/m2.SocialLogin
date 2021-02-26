<?php
/**
 * Copyright © Karliuka Vitalii(karliuka.vitalii@gmail.com)
 * See COPYING.txt for license details.
 */
namespace Faonni\SocialLogin\Block\Provider;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Faonni\SocialLogin\Helper\Data as SocialLoginHelper;
use Faonni\SocialLogin\Model\ProviderFactory;

/**
 * Provider List Block
 */
class ProviderList extends Template
{
    /**
     * Helper
     *
     * @var \Faonni\SocialLogin\Helper\Data
     */
    protected $_helper;

    /**
     * Provider Model
     *
     * @var \Faonni\SocialLogin\Model\Provider
     */
    protected $_provider;

    /**
     * Providers Collection
     *
     * @var \Faonni\SocialLogin\Model\ResourceModel\Provider\Collection
     */
    protected $_collection;

    /**
     * Initialize Block
     *
     * @param SocialLoginHelper $helper
     * @param ProviderFactory $providerFactory
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(
        SocialLoginHelper $helper,
        ProviderFactory $providerFactory,
        Context $context,
        array $data = []
    ) {
        $this->_helper = $helper;
        $this->_provider = $providerFactory->create();

        parent::__construct(
            $context,
            $data
        );
    }

    /**
     * Retrieve Provider Collection
     *
     * @return \Faonni\SocialLogin\Model\ResourceModel\Provider\Collection
     */
    public function getCollection()
    {
        if (null === $this->_collection) {
            $this->_collection = $this->_provider->getCollection();
            foreach ($this->_collection as $key => $provider) {
                if (!$provider->isAvailable()) {
                    $this->_collection->removeItemByKey($key);
                }
            }
        }
        return $this->_collection;
    }

    /**
     * Check Popup Mode
     *
     * @return bool
     */
    public function isPopupMode()
    {
        return $this->_helper->isPopupMode();
    }
}
