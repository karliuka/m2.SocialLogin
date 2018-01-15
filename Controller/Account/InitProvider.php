<?php
/**
 * Copyright © 2011-2018 Karliuka Vitalii(karliuka.vitalii@gmail.com)
 * 
 * See COPYING.txt for license details.
 */
namespace Faonni\SocialLogin\Controller\Account;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\Math\Random;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Controller\Result\Redirect as ResultRedirect;
use Magento\Framework\Url\HostChecker;
use Magento\Framework\Url\DecoderInterface;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\Url as CustomerUrl;
use Psr\Log\LoggerInterface;
use Faonni\SocialLogin\Controller\Account\AbstractAccount;
use Faonni\SocialLogin\Helper\Data as SocialLoginHelper;
use Faonni\SocialLogin\Model\ProviderFactory;
use Faonni\SocialLogin\Model\Provider;

/**
 * Init Provider Controller
 */
class InitProvider extends AbstractAccount
{
    /**
     * Url Decoder
     *
     * @var \Magento\Framework\Url\DecoderInterface
     */
    protected $_urlDecoder;
    
    /**
     * Host Checker
     *
     * @var \Magento\Framework\Url\HostChecker
     */
    protected $_hostChecker;    
    
    /**
     * Initialize controller
     *
     * @param Context $context
     * @param SocialLoginHelper $helper
     * @param ProviderFactory $providerFactory
     * @param DataObjectFactory $dataObjectFactory 
     * @param Session $customerSession
     * @param Random $mathRandom     
     * @param LoggerInterface $logger
     * @param DecoderInterface $urlDecoder
     * @param HostChecker|null $hostChecker     
     */
    public function __construct(
        Context $context,
        SocialLoginHelper $helper,
        ProviderFactory $providerFactory,
        DataObjectFactory $dataObjectFactory,
        Session $customerSession,
        Random $mathRandom,
        LoggerInterface $logger,
        DecoderInterface $urlDecoder,
        HostChecker $hostChecker
    ) {
        $this->_urlDecoder = $urlDecoder;
        $this->_hostChecker = $hostChecker ?: ObjectManager::getInstance()->get(HostChecker::class);
        
        parent::__construct(
            $context,
            $helper,
            $providerFactory,
            $dataObjectFactory,
            $customerSession,
            $mathRandom,
            $logger
        );
    }
    
    /**
     * Init Provider
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        if ($this->_helper->isEnabled() && $id) {
            try {
				$provider = $this->_provider->load($id);
				$display = $this->getRequest()->getParam('display');
				$referer = $this->getRequest()->getParam(CustomerUrl::REFERER_QUERY_PARAM_NAME);
				if ($referer) {
					$referer = $this->_urlDecoder->decode($referer);
					if ($this->_hostChecker->isOwnOrigin($referer)) {
						$this->_session->setAfterAuthUrl($referer);
					}
				}				
                $salt = $this->_mathRandom->getRandomString(32);
                $this->_session->setSocialLoginSalt($salt);
                $this->_session->setSocialLoginDisplay($display);
                
                /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();                                
                $resultRedirect->setUrl(
                    $provider->getProviderUrl(Provider::SCOPE_PREFIX, $salt)
                );
                return $resultRedirect;                  
            } 
            catch (Exception $e) {
                $this->_logger->addError(__('Error Loading the %1 Provider', $id));
            }             
        }
        throw new NotFoundException(__('Page not found.'));
    }
}
