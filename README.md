
# Riverstone AI Content Generator for Magento 2

## How to install & upgrade Riverstone_AiContentGenerator

### 1. Install via composer (recommend)

We recommend you to install Riverstone_AiContentGenerator module via composer. It is easy to install, update and maintaince.

Run the following command in Magento 2 root folder.

#### 1.1 Install

```
composer require riverstone/ai-content-Generator
php bin/magento setup:upgrade
php bin/magento setup:static-content:deploy

```
#### 1.2 Upgrade

```
composer update riverstone/ai-content-Generator
php bin/magento setup:upgrade
php bin/magento setup:static-content:deploy

```
Run compile if your store in Product mode:

```
php bin/magento setup:di:compile

```

