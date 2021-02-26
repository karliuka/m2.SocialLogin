<?php
/**
 * Copyright © Karliuka Vitalii(karliuka.vitalii@gmail.com)
 * See COPYING.txt for license details.
 */
use Magento\Framework\Component\ComponentRegistrar;

/**
 * Register module
 */
ComponentRegistrar::register(
    ComponentRegistrar::MODULE,
    'Faonni_SocialLogin',
    __DIR__
);
