# MyCloudAPI-SDK-PHP
PHP based SDK for the MyCloud Fulfillment API

## Prerequisites
   - PHP 5.6 or above
   - [curl](http://php.net/manual/en/book.curl.php), [json](http://php.net/manual/en/book.json.php) & [openssl](http://php.net/manual/en/book.openssl.php) extensions must be enabled

## Getting Started

   You are encouraged to use Composer to integrate this API with your PHP project.

   To include the project, make the following additions to your composer.json file:
```
      "require": {
         "mycloudth/rest-api-sdk-php": "dev-master"
      },
      "repositories": [
         {
            "type": "vcs",
            "url": "https://github.com/timbkbasic/MyCloudAPI-SDK-PHP.git"
         }
      ]
```
   Then run "composer install" or "composer update" to load the SDK into your project.
   Once you have installed the SDK via composer, you are ready to use the SDK in your
   project, without needing any "requires" in your code. However, you will need to set
   the constant MCAPI_CONFIG_PATH as noted below.

   If you are not using Composer, then you can obtain the "Binary Distribution" of the SDK.
   This will contain a top-level directory named "vendor". The binary distribution is simply
   a copy of the vendor contents created by Composer, then packaged to allow you to include
   it in your project without using Composer. Everything packaged in the binary distribution
   is relative to this top-level "vendor" directory, so you must be careful to not rearrange
   the distribution package, or the Composer built dependencies are very likely to fail.

   If you are working with the binary distribution of the SDK, you must copy the "vendor"
   directory into your project. In your code, you will need to use:
```
      require_once('path_to_vendor_directory/autoload.php');
```
   which will load the SDK and it's dependencies. After this require, you should be able to
   make calls against the API. However, before you require the autoload.php file, please be
   sure to set the constant MCAPI_CONFIG_PATH as noted below.

   The entire API depends on the file "sdk_config.ini" to properly configure the information that
   the API needs to work correctly. To ensure this ini file is available to the API, your code must
   define the constant "MCAPI_CONFIG_PATH" to point to the directory containing this configuration
   file. This constant must be defined _before_ you access any of the SDK objects or methods, and it
   must be defined before calling "require_once()" (if you are using the binary distribution). If
   this constant is not defined, then the config file will be looked for in
   `dirname(__FILE__)/../config/`, but this is not recommended.

   An example of an sdk_config.ini file is included in the "examples" directory, as well as in
   the "vendor" directory. Either file is an excellent starting point for your configuration.
   The config file contains many comments to help you understand each configuration item.

   Examples of every possible use of the API are provided in the top-level directory 'examples'.
   The file "bootstrap.php" is essential to every example to properly load the API, so if you
   are having trouble running the examples, this would be the place to start. The examples all
   use the sdk_config.ini file included in the examples directory.

   __NOTE__ That every RESTful call to the API can return either an API Model object (the model
   requested by the RESTful call), or it can return an instance of MyCloud\Api\Core\MCError.
   Furthermore, all RESTful calls can throw an Exception. Therefore, all of your RESTful API
   calls should be placed inside of a try-catch block, and the result of the call should always
   be checked against "instanceof MCError" to see if the result was an error. See the examples
   for the proper way to construct your API calls.

## Support
   Inquiries regarding this API should be directed to "dev@mycloudfulfillment.com"

## License

   Read [License.txt](LICENSE.txt) for licensing information.
