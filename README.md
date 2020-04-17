# CSETech - Free Currency Converter API Connector
MIT License - Copyright (C) 2019 Chen Si-En, Sarah

Converts currencies using the Free Currency Converter API. Fallback for when existing/default Magento currency converters do not work.

## Getting Started

1. Go to Stores > Configuration > General > Currency Setup.
Select allowed currencies (ie. other currencies needed) including base currency.
Enter API Key from Free Currency Converter API.
Save Configuration.

2. Go to Stores > Currency > Currency Rates.
Select Import Service: Free Currency Converter.
Press Import.
Save Currency Rates.

### Prerequisites

Magento ver. 2.2.6 and up.
API Key from https://free.currencyconverterapi.com

### Installing

1. Unzip files in Magento directory.

2. Run the following commands.

```
php bin/magento module:enable CSETech_CurrencyConverter -c
```
```
php bin/magento setup:di:compile
```
```
php bin/magento setup:upgrade
```
```
php bin/magento setup:static-content:deploy -f
```
```
php bin/magento cache:flush
```
