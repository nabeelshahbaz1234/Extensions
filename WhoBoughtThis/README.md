# WhoBoughtThis-Magento2

# Overview

Magento 2 Who Bought This Also Bought extension has been developed by the product team at RLTSquare. Based on the order status recorded after previous purchasing processes, suggested products will be displayed on the Product Page, Category Page and Home with multiple options. Hence, not only does this extension help boost your shop’s sales, but also satisfies your customers.

Here are some of the salient features for the extension:

```
1. Suggest products based on the previous placed orders
2. Number of products in the slider can be increased or decreased in the backend extension configuration
3. AJAX Lazy Load on slider where all the products are displayed
4. Visible on Home Page, Product Page and Category Page
5. Levels of Category Hierarchy to display suggested products
6. Exclude or include out of stock products
```

## Installation

### Magento® Marketplace

This extension will also be available on the Magento® Marketplace when approved.

### Manually

1. Go to Magento® 2 root folder

2. Require/Download this extension:

   Enter following commands to install extension.

   ```
   composer require rltsquare/who-bought-this
   ```

   Wait while composer is updated.
   
   #### OR
   
   You can also download code from this repo under Magento® 2 following directory:
    
    ```
    app/code/RLTSquare/WhoBoughtThis
    ```    

3. Enter following commands to enable the module:

   ```
   php bin/magento module:enable RLTSquare_WhoBoughtThis
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
https://www.rltsquare.com/blog/who-bought-this-also-bought-magento-2-extension/
