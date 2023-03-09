# ZAVER PAYMENT MODULE FOR OXID ESHOP
Zaver payment module for OXID eShop.

Zaver payment module is available for the Oxid eshop versions 6.2.x - 6.5.0 in the following languages in <b>EN, DE</b>

## Installation

### Option 1: Github Installation
### Prerequisites
Software requirements:
- installed Oxid eShop >= v6.2.x
- installed guzzlehttp/guzzle >=7.0 (composer require guzzlehttp/guzzle)
- installed composer 2.2.5 

 ##### 1. Download a version from GitHub. 
 Please use the attached ZIP files (zaver-oxid-X.Y.Z.zip) from the list of available releases: GitHub Releases.
 ##### 2. Create the folder "zaver" in the "source/modules" folder of the Oxid 6 installation.
 ##### 3. Create the folder "payment" in the new "source/modules/zaver" folder of the Oxid 6 installation
 ##### 4. Copy the content of the downloaded version in the newly created "payment" folder.
 ##### 5. In the composer.json file in the base folder of the shop add the autoload configuration or extend if already existing:
          
          "autoload": {
            "psr-4": {
              "Zaver\\SDK\\": "./source/modules/zaver/payment/lib/src/"
            }
          },
          
          And run the following command in project root directory:
          
          composer dump-autoload
          
##### 6. Register the guzzlehttp/guzzle library in module composer.json file by modifying it:
            "require": {
              "guzzlehttp/guzzle": "^7.0"
            },
          
or run on the command line:  

          composer require guzzlehttp/guzzle:^7.0

Then run the following command in the project root directory:
           
          composer update
          
##### 7. In OXID versions from 6.2 on you must now import the module configuration. 
         
         vendor/bin/oe-console oe:module:install-configuration source/modules/zaver/payment/
         vendor/bin/oe-console oe:module:apply-configuration

##### 8. Empty the tmp folder

```
rm source/tmp/smarty/*
rm source/tmp/*
```


### Option 2: Install via Composer 

Follow the below steps and run each command from the shop root directory:
 ##### 1. Run the below command to install the payment module
 From zip file, copy the zip file 'zaver_module-zaver-oxid-1.0.2.zip' to the shop root directory.
 ```
 composer config repositories.gclocal artifact ./
 composer require zaver/zaver-oxid:1.0.2
 ```
 From git repository.
 ```
  composer require zaver/zaver-oxid:1.0.2
  ```
 ##### 2. Run the below command to register the payment module from the version above 6.2 oxid eshop
 ```
 ./vendor/bin/oe-console oe:module:install source/modules/zaver/payment
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
