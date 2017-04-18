# Conversant Data Layer

A Conversant module for Magento that integrates with our GTM DataLayer module: https://github.com/Space48/GtmDataLayer - it adds the following data layer variables:

### Homepage
- promo_id

### Order Success

- promo_id
    
### Cart

- promo_id

### Category View

- promo_id
- department
- category
- sub_category

### Product View

- promo_id
- brand
- related_products

### Search Result

- promo_id

## Installation

**Manual**

To install this module copy the code from this repo to `app/code/Space48/ConversantDataLayer` folder of your Magento 2 instance, then you need to run php `bin/magento setup:upgrade`

**Composer**:

From the terminal execute the following:

`composer config repositories.space48-conversant-datalayer vcs git@github.com:Space48/ConversantDataLayer.git`

then

`composer require "space48/conversantdatalayer:{module-version}"`

## How to use

Go to `Stores > Configuration > Space48 > GTM DataLayer > Conversant` to configure/enable.
