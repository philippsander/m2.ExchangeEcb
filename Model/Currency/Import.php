<?php
/**
 * Copyright © Eriocnemis, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Eriocnemis\ExchangeEcb\Model\Currency;

use Magento\Directory\Model\Currency\Import\AbstractImport;
use Magento\Directory\Model\CurrencyFactory;

/**
 * Currency rate import
 */
class Import extends AbstractImport
{
    /**
     * Currency rate
     *
     * @var Rate
     */
    protected $rate;

    /**
     * Currency rate factory
     *
     * @var RateFactory
     */
    protected $rateFactory;

    /**
     * Ecb base currency code
     *
     * @var string
     */
    protected $baseCurrencyCode = 'EUR';

    /**
     * Initialize import
     *
     * @param CurrencyFactory $currencyFactory
     * @param RateFactory $rateFactory
     */
    public function __construct(
        CurrencyFactory $currencyFactory,
        RateFactory $rateFactory
    ) {
        $this->rateFactory = $rateFactory;

        parent::__construct(
            $currencyFactory
        );
    }

    /**
     * Retrieve currency codes
     *
     * @return array
     */
    protected function _getCurrencyCodes()
    {
        return array_intersect(
            array_merge([$this->baseCurrencyCode], $this->getRate()->getAllCodes()),
            parent::_getCurrencyCodes()
        );
    }

    /**
     * Retrieve default currency codes
     *
     * @return array
     */
    protected function _getDefaultCurrencyCodes()
    {
        return array_intersect(
            [$this->baseCurrencyCode],
            parent::_getDefaultCurrencyCodes()
        );
    }

    /**
     * Exchange currency
     *
     * @param string $currencyFrom
     * @param string $currencyTo
     * @return float
     */
    protected function _convert($currencyFrom, $currencyTo)
    {
        return $this->getRate()->getRateByCode($currencyTo);
    }

    /**
     * Retrieve rate
     *
     * @return Rate
     */
    protected function getRate()
    {
        if (null === $this->rate) {
            $this->rate = $this->rateFactory->create()->load();
            if ($this->rate->getError()) {
                $this->_messages[] = __('Currency rates can\'t be retrieved.');
            }
        }
        return $this->rate;
    }
}
