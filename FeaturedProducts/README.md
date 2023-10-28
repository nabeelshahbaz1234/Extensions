# FeaturedProducts-Magento2

# Overview

Featured Products extension for Magento 2 displays products which are important to highlight on the home page, product page and category Page. So, customers can view and easily buy them if they want. 
It helps you to increase your sales by showing the customers your featured products on the home page. You can easily set the featured products manually as per your need, and our extension provides many options for settings in the admin panel. There’s an option to enable/disable any product to be a featured product.

Here are some of the salient features for the extension:

```
1. Show featured products in your store
2. Number of featured products in the slider can be increased or decreased in the extension configuration
3. Apply AJAX Lazy Load on slider where all the products are displayed
4. Visible on Home, Product and Category Page
5. Exclude or include out of stock products from the slider
```

## Installation

### Magento® Marketplace

This extension will also be available on the Magento® Marketplace when approved.

### Manually

1. Go to Magento® 2 root folder

2. Require/Download this extension:

   Enter following commands to install extension.

   ```
   composer require rltsquare/featured-products
   ```

   Wait while composer is updated.
   
   #### OR
   
   You can also download code from this repo under Magento® 2 following directory:
    
    ```
    app/code/RLTSquare/FeaturedProducts
    ```    

3. Enter following commands to enable the module:

   ```
   php bin/magento module:enable RLTSquare_FeaturedProducts
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
https://www.rltsquare.com/blog/featured-products-magento-2-extension
