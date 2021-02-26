<?php
/**
 * Copyright © Karliuka Vitalii(karliuka.vitalii@gmail.com)
 * See COPYING.txt for license details.
 */
namespace Faonni\SocialLogin\Controller\Account;

use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Controller\ResultFactory;
use Faonni\SocialLogin\Controller\Account\AbstractAccount;

/**
 * Social Profile Controller
 */
class SocialProfile extends AbstractAccount
{
    /**
     * Customer account profile
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if (!$this->_helper->isEnabled()) {
            throw new NotFoundException(__('Page not found.'));
        }
        /** @var \Magento\Framework\View\Result\Page resultPage */
        return $this->resultFactory->create(ResultFactory::TYPE_PAGE);
    }
}
