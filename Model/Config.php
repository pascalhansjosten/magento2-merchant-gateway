<?php

namespace Heidelpay\MGW\Model;

use Heidelpay\MGW\Model\Logger\DebugHandler;
use heidelpayPHP\Heidelpay;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Locale\Resolver;

/**
 * Global Module configuration and SDK provider
 *
 * Copyright (C) 2019 heidelpay GmbH
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @link  https://docs.heidelpay.com/
 *
 * @author Justin Nuß
 *
 * @package  heidelpay/magento2-merchant-gateway
 */
class Config extends \Magento\Payment\Gateway\Config\Config
{
    const BASE_CONFIGURATION_PATH = 'payment/hpmgw/';

    const KEY_PUBLIC_KEY = 'public_key';
    const KEY_PRIVATE_KEY = 'private_key';
    const KEY_LOGGING = 'logging';

    const METHOD_BASE = 'hpmgw';
    const METHOD_CARDS = 'hpmgw_cards';
    const METHOD_DIRECT_DEBIT = 'hpmgw_direct_debit';
    const METHOD_DIRECT_DEBIT_GUARANTEED = 'hpmgw_direct_debit_guaranteed';
    const METHOD_FLEXIPAY_DIRECT = 'hpmgw_flexipay_direct';
    const METHOD_IDEAL = 'hpmgw_ideal';
    const METHOD_INVOICE = 'hpmgw_invoice';
    const METHOD_INVOICE_GUARANTEED_B2B = 'hpmgw_invoice_guaranteed_b2b';
    const METHOD_INVOICE_GUARANTEED = 'hpmgw_invoice_guaranteed';
    const METHOD_PAYPAL = 'hpmgw_paypal';
    const METHOD_SOFORT = 'hpmgw_sofort';

    /**
     * @var DebugHandler
     */
    private $_debugHandler;

    /**
     * @var Resolver
     */
    private $_localeResolver;

    /**
     * @var ScopeConfigInterface
     */
    private $_scopeConfig;

    /**
     * Config constructor.
     * @param Resolver $localeResolver
     * @param ScopeConfigInterface $scopeConfig
     * @param DebugHandler $debugHandler
     * @param null $methodCode
     * @param string $pathPattern
     */
    public function __construct(
        Resolver $localeResolver,
        ScopeConfigInterface $scopeConfig,
        DebugHandler $debugHandler,
        $methodCode = null,
        $pathPattern = self::DEFAULT_PATH_PATTERN
    ) {
        parent::__construct($scopeConfig, $methodCode, $pathPattern);

        $this->_debugHandler = $debugHandler;
        $this->_localeResolver = $localeResolver;
        $this->_scopeConfig = $scopeConfig;
    }

    /**
     * @return bool
     */
    private function isDebugMode(): bool
    {
        return $this->_scopeConfig->isSetFlag(self::BASE_CONFIGURATION_PATH . self::KEY_LOGGING);
    }

    /**
     * Returns the public key.
     *
     * @return string
     */
    public function getPublicKey(): string
    {
        return $this->_scopeConfig->getValue(self::BASE_CONFIGURATION_PATH . self::KEY_PUBLIC_KEY);
    }

    /**
     * Returns the private key.
     *
     * @return string
     */
    public function getPrivateKey(): string
    {
        return $this->_scopeConfig->getValue(self::BASE_CONFIGURATION_PATH . self::KEY_PRIVATE_KEY);
    }

    /**
     * Returns an API client using the configured private key.
     *
     * @return Heidelpay
     * @throws NoSuchEntityException
     */
    public function getHeidelpayClient(): Heidelpay
    {
        /** @var Heidelpay $client */
        $client = new Heidelpay(
            $this->getPrivateKey(),
            $this->_localeResolver->getLocale()
        );

        $client->setDebugMode($this->isDebugMode());
        $client->setDebugHandler($this->_debugHandler);

        return $client;
    }
}
