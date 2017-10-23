# MyCloudAPI-SDK-PHP
PHP based SDK for the MyCloud Fulfillment API

## Prerequisites
   - PHP 5.6 or above
   - [curl](http://php.net/manual/en/book.curl.php), [json](http://php.net/manual/en/book.json.php) & [openssl](http://php.net/manual/en/book.openssl.php) extensions must be enabled

## Getting Started

   You are encouraged to use Composer to integrate this API with your PHP project.

   To include the project, make the following additions to your composer.json file:

      "require": {
         "mycloudth/rest-api-sdk-php": "dev-master"
      },
      "repositories": [
         {
            "type": "vcs",
            "url": "https://github.com/timbkbasic/MyCloudAPI-SDK-PHP.git"
         }
      ]

   Then run "composer install" or "composer update" to load the SDK into your project.
   Once you have installed the SDK via composer, you are ready to use the SDK.

   If you received the binary distribution of the SDK, you have a top level directory named
   "vendor", which you should copy into your project. In your code, you will need to use:
   
      require_once('path_to_vendor_directory/autoload.php');

   which will load the SDK and it's dependencies. After this require, you should be able to
   code against the API.

   The entire API depends on the file "sdk_config.ini" to propery configure the information that
   the API needs to run properly. To ensure this ini file is available to the API, your code must
   define the constant "MCAPI_CONFIG_PATH" to point to the directory containing this configuration
   file. This contant MUST BE DEFINED BEFORE you access any of the SDK objects or methods, and it
   must be defined before calling "require_once()" (if you are using the binary ditribution).

   Examples of every possible use of the API are provided in the top-level directory 'examples'.
   The file "bootstrap.php" is essential to every example to properly load the API, so if you
   are having trouble running the examples, this would be the place to start. The examples all
   use the sdk_config.ini file included in the examples directory.

## Support
   Inquiries regarding this API should be directed to "dev@mycloudfulfillment.com"

## License

   Read [License.txt](LICENSE.txt) for licensing information.
