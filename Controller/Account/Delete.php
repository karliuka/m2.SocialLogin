<?php
/**
 * Copyright © 2011-2017 Karliuka Vitalii(karliuka.vitalii@gmail.com)
 * 
 * See COPYING.txt for license details.
 */
namespace Faonni\SocialLogin\Controller\Account;

use Faonni\SocialLogin\Controller\Account\AbstractAccount;

/**
 * Delete Controller
 */
class Delete extends AbstractAccount
{
    /**
     * Delete Action
     *
     * @throws \Magento\Framework\Exception\NotFoundException
     * @return \Magento\Framework\Controller\AbstractResult
     */
    public function execute()
    {
		$id = $this->getRequest()->getParam('id', false);
    }
}