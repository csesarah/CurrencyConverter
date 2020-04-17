<?php
namespace CSETech\CurrencyConverter\Model\Currency\Import;
class FreeCurrencyConverter extends \Magento\Directory\Model\Currency\Import\AbstractImport
{
   /**
       * @var string
       */
      const CURRENCY_CONVERTER_URL = 'https://free.currconv.com/api/v7/convert?q={{CURRENCY_FROM}}_{{CURRENCY_TO}}&compact=ultra&apiKey={{API_KEY}}';



      /** @var \Magento\Framework\Json\Helper\Data */
      protected $jsonHelper;

      /**
       * Http Client Factory
       *
       * @var \Magento\Framework\HTTP\ZendClientFactory
       */
      protected $httpClientFactory;

      /**
       * Core scope config
       *
       * @var \Magento\Framework\App\Config\ScopeConfigInterface
       */
      private $scopeConfig;

      /**
       * Initialize dependencies
       *
       * @param \Magento\Directory\Model\CurrencyFactory $currencyFactory
       * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
       * @param \Magento\Framework\HTTP\ZendClientFactory $httpClientFactory
       * @param \Magento\Framework\Json\Helper\Data $jsonHelper
       */
      public function __construct(
          \Magento\Directory\Model\CurrencyFactory $currencyFactory,
          \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
          \Magento\Framework\HTTP\ZendClientFactory $httpClientFactory,
          \Magento\Framework\Json\Helper\Data $jsonHelper
      ) {
          parent::__construct($currencyFactory);
          $this->scopeConfig = $scopeConfig;
          $this->httpClientFactory = $httpClientFactory;
          $this->jsonHelper = $jsonHelper;
      }

      /**
       * @param string $currencyFrom
       * @param string $currencyTo
       * @param int $retry
       * @return float|null
       */
      protected function _convert($currencyFrom, $currencyTo, $retry = 0)
      {
          $result = null;
          $accessKey = $this->scopeConfig->getValue(
              'currency/fcc/api_key',
              \Magento\Store\Model\ScopeInterface::SCOPE_STORE
          );

          if (empty($accessKey)) {
            $this->_messages[] = __('No API key was specified or an invalid API key was specified');
          }

          $url = str_replace('{{API_KEY}}', $accessKey, self::CURRENCY_CONVERTER_URL);
          $url = str_replace('{{CURRENCY_FROM}}', $currencyFrom, $url);
          $url = str_replace('{{CURRENCY_TO}}', $currencyTo, $url);
          $timeout = (int)$this->scopeConfig->getValue(
              'currency/fcc/timeout',
              \Magento\Store\Model\ScopeInterface::SCOPE_STORE
          );

          /** @var \Magento\Framework\HTTP\ZendClient $httpClient */
          $httpClient = $this->httpClientFactory->create();

          try {

              $resultKey = $currencyFrom . '_' . $currencyTo;
              $response = file_get_contents($url);
              $data = $this->jsonHelper->jsonDecode($response);
              $results = $data[$resultKey];
              $result = (float)$results;

          } catch (\Exception $e) {
              if ($retry == 0) {
                  $this->_convert($currencyFrom, $currencyTo, 1);
              } else {
                  $this->_messages[] = __('We can\'t retrieve a rate from %1.', $url);
              }
          }
          return $result;
    }
}
