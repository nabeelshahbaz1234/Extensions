# DiscountFilter-Magento2

# Overview

Discount filter extension for Magento 2 has been developed by the product team at RLTSquare. This extension is essential for your store because customers always prefer special offers and reduced prices. Discounted prices for a product has a special effect on the customers and they like to buy products from a store which has given special discounts. You can lose a lot of customers if you don’t have products on sale prices and they can move to a competitors’ store.
If you are losing customers and you don’t know the exact reason for it then may be you need to consider this extension. Your competitors’ may have a filter by discount on their Layered Navigation menu. This extension allows merchants to quickly add discounts to the products and make it easier for the customers to search for them. 
Rest assured, this extension will increase the conversion rates of your websites by making it easier for the customers to filter by discount percentage. They don’t need to go through all of the products to search for reduced prices. This filtering saves time and improves the shopping experience. Hence, boosting the sales of your store.

Here are some of the salient features for the extension:

```
1. Seamless integration of your online store with Discount Filter extension 
2. Add the discount filter for customers to search through the catalog more accurately
3. Use discount percentage range to filter products by the discount
4. Show discounts in percentage or amount
5. We will help you set up this extension until the extension works properly
```

## Installation

### Magento® Marketplace

This extension will also be available on the Magento® Marketplace when approved.

### Manually

1. Go to Magento® 2 root folder

2. Require/Download this extension:

   Enter following commands to install extension.

   ```
   composer require rltsquare/discountfilter
   ```

   Wait while composer is updated.
   
   #### OR
   
   You can also download code from this repo under Magento® 2 following directory:
    
    ```
    app/code/RLTSquare/DiscountFilter
    ```

3. Enter following commands to enable the module:

   ```
   php bin/magento module:enable RLTSquare_DiscountFilter
   php bin/magento setup:upgrade
   php bin/magento cache:clean
   php bin/magento cache:flush
   ```

4. If Magento® is running in production mode, deploy static content: 

   ```
   php bin/magento setup:static-content:deploy
   ```


## Requirements

1. This Magento® extension works on Magento 2.2 and 2.3 versions. Tested on versions 2.2.5 and above.

2. Tested on different themes specifically Ultimo, Porto and certain custom themes

For details, read our blog:
https://www.rltsquare.com/blog/discount-filter-magento-2-extension/
