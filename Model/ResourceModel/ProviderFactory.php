<?php
/**
 * Copyright © Karliuka Vitalii(karliuka.vitalii@gmail.com)
 * See COPYING.txt for license details.
 */
namespace Faonni\SocialLogin\Model\ResourceModel;

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Oauth2 Provider Resource factory
 */
class ProviderFactory
{
    /**
     * Object Manager instance
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * Provider factory config
     *
     * @var \Faonni\SocialLogin\Model\ProviderConfig
     */
    protected $_config;

    /**
     * Initialize factory
     *
     * @param ObjectManagerInterface $objectManager
     * @param ProviderConfig $config
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        ProviderConfig $config
    ) {
        $this->_objectManager = $objectManager;
        $this->_config = $config;
    }

    /**
     * Create new provider resource object
     *
     * @param string $providerName
     * @param array $data
     * @throws \InvalidArgumentException
     * @throws LocalizedException
     * @return ProviderInterface
     */
    public function create($providerName, array $data = [])
    {
        $providerClass = $this->_config->getProviderClass($providerName);
        if (!$providerClass) {
            throw new \InvalidArgumentException("Provider Resource '{$providerName}' is not defined.");
        }

        $providerInstance = $this->_objectManager
            ->create($providerClass, $data);

        if (!$providerInstance instanceof ProviderInterface) {
            throw new LocalizedException(
                __('Provider must implement %1.', ProviderInterface::class)
            );
        }
        return $providerInstance;
    }
}
