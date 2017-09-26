<?php
/**
 * Copyright © 2011-2017 Karliuka Vitalii(karliuka.vitalii@gmail.com)
 * 
 * See COPYING.txt for license details.
 */
namespace Faonni\SocialLogin\Model\ResourceModel;

/**
 * Oauth2 Provider Resource factory config
 */
class ProviderConfig
{
    /**
     * @var array
     */
    private $_config;

    /**
     * Validate format of providers configuration array
     *
     * @param array $config
     * @throws \InvalidArgumentException
     */
    public function __construct(array $config)
    {
        foreach ($config as $providerName => $providerInfo) {
            if (!is_string($providerName) || empty($providerName)) {
                throw new \InvalidArgumentException('Name for a import provider has to be specified.');
            }
            if (empty($providerInfo['class'])) {
                throw new \InvalidArgumentException('Class for a import provider has to be specified.');
            }
        }
        $this->_config = $config;
    }

    /**
     * Retrieve unique names of all available import providers
     *
     * @return array
     */
    public function getAvailableProviders()
    {
        return array_keys($this->_config);
    }

    /**
     * Retrieve name of a class that corresponds to provider name
     *
     * @param string $providerName
     * @return string|null
     */
    public function getProviderClass($providerName)
    {
        if (isset($this->_config[$providerName]['class'])) {
            return $this->_config[$providerName]['class'];
        }
        return null;
    }
}
 
