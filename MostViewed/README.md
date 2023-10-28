# MostViewed-Magento2

# Overview

The Most Viewed Products for Magento 2 extension has been developed by the product team at RLTSquare. This extension is useful to show products which are viewed most by customers in your store. You can highlight the Most Viewed Products in your store, attracting customers to buy these products. It helps you to increase your sales. You can easily set the most viewed products manually as per your need. You can easily setup most viewed products, and our extension provides many options for settings from the admin.

Here are some of the salient features for the extension:

```
1. To show products which are most viewed by the customers in your store.
2. Number of products in the slider can be minimize and maximize from admin extension configuration.
3. Lazyload on slider where all the products display.
4. Visible on Product Page,Home and Category Page.
```

## Installation

### Magento® Marketplace

This extension will also be available on the Magento® Marketplace when approved.

### Manually

1. Go to Magento® 2 root folder

2. Require/Download this extension:

   Enter following commands to install extension.

   ```
   composer require rltsquare/most-viewed
   ```

   Wait while composer is updated.
   
   #### OR
   
   You can also download code from this repo under Magento® 2 following directory:
    
    ```
    app/code/RLTSquare/MostViewed
    ```    

3. Enter following commands to enable the module:

   ```
   php bin/magento module:enable RLTSquare_MostViewed
   php bin/magento setup:upgrade
   php bin/magento cache:clean
   php bin/magento cache:flush
   ```

4. If Magento® is running in production mode, deploy static content: 

   ```
   php bin/magento setup:static-content:deploy
   ```


## Requirements

1. This Magento® extension works on Magento 2.1,2.2 and 2.3 versions. Tested on versions 2.2.5 and above.

2. Tested on different themes specifically Ultimo, Porto and certain custom themes

For details, read our blog:
https://www.rltsquare.com/blog/most-viewed-magento-2-extension/
