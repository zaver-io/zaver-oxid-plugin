# ZAVER PAYMENT MODULE FOR OXID ESHOP
Zaver payment module for OXID eShop.

Zaver payment module is available for the Oxid eshop versions 6.2.x - 6.5.0 in the following languages in <b>EN, DE</b>

## Installation

### Prerequisites
Software requirements:
- installed Oxid eShop >= v6.2.x
- installed guzzlehttp/guzzle >=7.0 (composer require guzzlehttp/guzzle)
- installed composer 2.2.5 

 ##### 1. Copy the content of the Plugin in the "source/modules" folder.
 ##### 2. In the composer.json file in the base folder of the shop add the autoload configuration or extend if already existing:
          
          "autoload": {
            "psr-4": {
              "Zaver\\SDK\\": "./source/modules/zaver/payment/lib/src/"
              "Zaver\\Payment\\": "./source/modules/Zaver/Payment/"
            }
          },
          
          "autoload-dev": {
            "psr-4": {
              "Zaver\\SDK\\": "./source/modules/zaver/payment/lib/src/",
              "Zaver\\Payment\\": "./source/modules/Zaver/Payment/"
            }
          },
          
          And run the following command in project root directory:
          
          composer dump-autoload
          
##### 3. Register the guzzlehttp/guzzle library in module composer.json file by modifying it:
            "require": {
              "guzzlehttp/guzzle": "^7.0"
            },
          
or run on the command line:  

          composer require guzzlehttp/guzzle:^7.0

Then run the following command in the project root directory:
           
          composer update
          
##### 4. In OXID versions from 6.2 on you must now import the module configuration. 
         
         vendor/bin/oe-console oe:module:install-configuration source/modules/zaver/payment/
         vendor/bin/oe-console oe:module:apply-configuration

##### 5. Empty the tmp folder

```
rm source/tmp/smarty/*
rm source/tmp/*
```

### Uninstall the module
Again from the command line in the shop's main directory:
```
./vendor/bin/oe-console oe:module:uninstall-configuration zaver
composer remove zaver/zaver-oxid
composer remove zaver/sdk
rm -rf source/modules/zaver
rm source/tmp/smarty/*
rm source/tmp/*
```

### Finalizing Steps
1. Go to "Extensions->Modules", select the "Zaver payments" extension and press the "Activate" Button in the "Overview" tab.
2. There is a new menu item in the OXID-Interface named "Zaver". Here you can set your merchant connect data.
3. Press the button "Syncronize payments" and your zaver payments are added to the Oxid payment methods.
4. Go to the menu "Shop Settings->Shipping methods" and configure the zaver payments in the shipping methods.
