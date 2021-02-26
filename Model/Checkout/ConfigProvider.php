<?php
/**
 * Copyright © Karliuka Vitalii(karliuka.vitalii@gmail.com)
 * See COPYING.txt for license details.
 */
namespace Faonni\SocialLogin\Model\Checkout;

use Magento\Framework\Url\EncoderInterface;
use Magento\Framework\UrlInterface;
use Magento\Customer\Model\Url;
use Magento\Checkout\Model\ConfigProviderInterface;
use Faonni\SocialLogin\Helper\Data as SocialLoginHelper;
use Faonni\SocialLogin\Model\ProviderFactory;

/**
 * SocialLogin Config Provider
 */
class ConfigProvider implements ConfigProviderInterface
{
    /**
     * Helper
     *
     * @var \Faonni\SocialLogin\Helper\Data
     */
    protected $_helper;

    /**
     * Provider model
     *
     * @var \Faonni\SocialLogin\Model\Provider
     */
    protected $_provider;

    /**
     * Url Encoder
     *
     * @var \Magento\Framework\Url\EncoderInterface
     */
    protected $_urlEncoder;

    /**
     * URL builder
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;

    /**
     * Initialize Config
     *
     * @param SocialLoginHelper $helper
     * @param ProviderFactory $providerFactory
     * @param EncoderInterface $urlEncoder
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        SocialLoginHelper $helper,
        ProviderFactory $providerFactory,
        EncoderInterface $urlEncoder,
        UrlInterface $urlBuilder
    ) {
        $this->_helper = $helper;
        $this->_provider = $providerFactory->create();
        $this->_urlEncoder = $urlEncoder;
        $this->_urlBuilder = $urlBuilder;
    }

    /**
     * Retrieve Assoc Array Of Checkout Configuration
     *
     * @return array
     */
    public function getConfig()
    {
        return ['sociallogin' => [
            'popup' => $this->_helper->isPopupMode(),
            'providers' => $this->getCollection()
        ]];
    }

    /**
     * Generate Url By Route And Parameters
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->_urlBuilder->getUrl($route, $params);
    }

    /**
     * Retrieve Provider Collection
     *
     * @return \Faonni\SocialLogin\Model\ResourceModel\Provider\Collection
     */
    public function getCollection()
    {
        $providers = [];
        $params = [
            Url::REFERER_QUERY_PARAM_NAME =>
            $this->_urlEncoder->encode(
                $this->getUrl('*/*/*', ['_current' => true, '_fragment' => 'shipping'])
            )
        ];
        $collection = $this->_provider->getCollection();
        foreach ($collection as $key => $provider) {
            if (!$provider->isAvailable()) {
                continue;
            }
            $providers[] = [
                'id' => $provider->getId(),
                'width' => $provider->getWidth(),
                'height' => $provider->getHeight(),
                'url' => $provider->getUrl($params),
                'title' => $provider->getTitle()
            ];
        }
        return $providers;
    }
}
