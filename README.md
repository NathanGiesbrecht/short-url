# Short URL

<div style="text-align:center">

[![Latest Version on Packagist](https://img.shields.io/packagist/v/ashallendesign/short-url.svg?style=flat-square)](https://packagist.org/packages/ashallendesign/short-url)
[![Build Status](https://img.shields.io/travis/ash-jc-allen/short-url/master.svg?style=flat-square)](https://travis-ci.org/ash-jc-allen/short-url)
[![Total Downloads](https://img.shields.io/packagist/dt/ashallendesign/short-url.svg?style=flat-square)](https://packagist.org/packages/ashallendesign/short-url)
[![PHP from Packagist](https://img.shields.io/packagist/php-v/ashallendesign/short-url?style=flat-square)](https://img.shields.io/packagist/php-v/ashallendesign/short-url)
[![GitHub license](https://img.shields.io/github/license/ash-jc-allen/short-url?style=flat-square)](https://github.com/ash-jc-allen/short-url/blob/master/LICENSE)

</div>

## Table of Contents

- [Overview](#overview)
- [Installation](#installation)
    - [Requirements](#requirements)
    - [Install the Package](#install-the-package)
    - [Publish the Config](#publish-the-config)
    - [Migrate the Database](#migrate-the-database)
- [Usage](#usage)
    - [Building Shortened URLs](#building-shortened-urls)
        - [Quick Start](#quick-start)
        - [Custom Keys](#custom-keys)
        - [Tracking Visitors](#tracking-visitors)
        - [Single Use](#single-use)
        - [Enforce HTTPS](#enforce-https)
    - [Using the Shortened URLs](#using-the-shortened-urls)
        - [Default Route and Controller](#default-route-and-controller)
        - [Custom Route](#custom-route)
    - [Tracking](#tracking)
    - [Customisation](#customisation)
        - [Disabling the Default Route](#disabling-the-default-route)
        - [Default URL Key Length](#default-url-key-length)
        - [Tracking Visits](#tracking-visits)
            - [Default Tracking](#default-tracking)
            - [Tracking Fields](#tracking-fields)
    - [Helper Methods](#helper-methods)
        - [Visits](#visits)
        - [Find by URL Key](#find-by-url-key)
        - [Find by Destination URL](#find-by-destination-url)
- [Testing](#testing)
- [Security](#security)
- [Contribution](#contribution)
- [License](#license)
    
## Overview

A Laravel package that can be used for adding shortened URLs to your existing web app.

## Installation

### Requirements
The package has been developed and tested to work with the following minimum requirements:

- PHP 7.2
- Laravel 5.8

### Install the Package
You can install the package via Composer:

```bash
composer require ashallendesign/short-url
```

### Publish the Config
You can then publish the package's config file (so that you can make changes to it) by using the following command:
```bash
php artisan vendor:publish --provider="AshAllenDesign\ShortURL\Providers\ShortURLProvider"
```

### Migrate the Database
This package contains two migrations that add two new tables to the database: ``` short_urls ``` and ``` short_url_visits ```. To run these migrations, simply run the following command:
```bash
php artisan migrate
```

## Usage
### Building Shortened URLs
#### Quick Start
The quickest way to get started with creating a shortened URL is by using the snippet below. The ``` ->make() ``` method
 returns a ShortURL model that you can grab the shortened URL from.
```php
$builder = new \AshAllenDesign\ShortURL\Classes\Builder();

$shortURLObject = $builder->destinationUrl('https://destination.com')->make();
$shortURL = $shortURLObject->default_short_url;
```

#### Custom Keys
By default, the shortened URL that is generated will contain a random key. The key will be of the length that you define
in the config files (defaults to 5 characters). Example: if a URL is ``` https://webapp.com/short/abc123 ```, the key is
``` abc123 ```.

You may wish to define a custom key yourself for that URL that is more meaningful than a randomly generated one. You can
do this by using the ``` ->urlKey() ``` method. Example:

```php
$builder = new \AshAllenDesign\ShortURL\Classes\Builder();

$shortURLObject = $builder->destinationUrl('https://destination.com')->urlKey('custom-key')->make();
$shortURL = $shortURLObject->default_short_url;

// Short URL: https://webapp.com/short/custom-key
```

Note: All of the URL keys are unique, so you cannot use a key that has already exists in the database for another shortened
URL.

#### Tracking Visitors
You may want to track some data about the visitors that have used the shortened URL. This can be useful for analytics.
By default, tracking is enabled and all of the available tracking fields are also enabled. You can toggle the different
parts of the tracking in the config file. Read further on in the [Customisation](#customisation) section to see how to
customise the default tracking behaviours.

If you want to override whether if tracking is enabled or not when creating a shortened URL, you can use the ``` ->trackVisits() ``` method.
This method simply accepts a boolean.

The example below shows how to enable tracking for the URL and override the config variable:
```php
$builder = new \AshAllenDesign\ShortURL\Classes\Builder();

$shortURLObject = $builder->destinationUrl('https://destination.com')->trackVisits()->make();
```

The example below shows how to disable tracking for the URL and override the config variable:
```php
$builder = new \AshAllenDesign\ShortURL\Classes\Builder();

$shortURLObject = $builder->destinationUrl('https://destination.com')->trackVisits(false)->make();
```

#### Single Use
By default, all of the shortened URLs can be visited for as long as you leave them available. However, you may want to
only allow access to a shortened URL once. Then any visitors who visit the URL after it has already been viewed will
get a HTTP 404.

To create a single use shortened URL, you can use the ``` ->singleUse() ``` method.

The example below shows how to create a single use shortened URL:
 ```php
 $builder = new \AshAllenDesign\ShortURL\Classes\Builder();
 
 $shortURLObject = $builder->destinationUrl('https://destination.com')->singleUse()->make();
 ```

#### Enforce HTTPS
When building a shortened URL, you might want to enforce that enforce that the visitor is redirected to the HTTPS version
of the destination URL. This can be particularly useful if you're allowing your web app users to create their own shortened
URLS.

To enforce HTTPS, you can use the ``` ->secure() ``` method when building the shortened URL.

The example below show how to create a secure shortened URL:
 ```php
$builder = new \AshAllenDesign\ShortURL\Classes\Builder();
 
$shortURLObject = $builder->destinationUrl('http://destination.com')->secure()->make();

// Desination URL: https://destination.com
 ```

### Using the Shortened URLs
#### Default Route and Controller
By default, the shortened URLs that are created use the package's route and controller. The routes use the following structure:
``` https://webapp.com/short/{urlKey} ```. This route uses the single-use controller that is found at 
``` \AshAllenDesign\ShortURL\Controllers\ShortURLController ```.

#### Custom Route
You may wish to use a different routing structure for your shortened URLs other than the default URLs that are created.
For example, you might want to use ``` https://webapp.com/s/{urlKey} ``` or ``` https://webapp.com/{urlKey} ```. You can
customise this to suit the needs of your project.

To use the custom routing all you need to do is add a web route to your project that points to the ShortURLController and
uses the ``` {shortURLKey} ``` field.

The example below shows how you could add a custom route to your ``` web.php ``` file to use the shortened URLs:
```php
Route::get('/custom/{shortURLKey}', '\AshAllenDesign\ShortURL\Controllers\ShortURLController');
```

Note: If you use your own custom routing, you might want to disable the default route that the app provides. Details are
provided for this in the [Customisation](#customisation) section below.

### Tracking
If tracking is enabled for a shortened URL, each time the link is visited, a new ShortURLVisit row in the database will
be created. By default, the package is set to record the following fields of a visitor:

- IP Address
- Browser Name
- Browser Version
- Operating System Name
- Operating System Version

Each of these fields can be toggled in the config files so that you only record the fields you need. Details on how to 
do this are provided for this in the [Customisation](#customisation) section below.

### Customisation
#### Disabling the Default Route
If you have added your own custom route to your project, you may want to block the default route that the package provides.
You can do this by setting the setting the following value in the config:

```
'disable_default_route' => true,
```
If the default route is disabled, any visitors who go to the ```/short/{urlKey}``` route will receive a HTTP 404.

#### Default URL Key Length 
When building a shortened URL, you have the option to define your own URL key or to randomly generate one. If one is
randomly generated, the length of it is determined from the config.

For example, to create a shortened URL with a key length of 10 characters, you could set the following in the config:

```
'key_length' => 10,
``` 

By default, the shortened URLs that are created have a key length of 5.

#### Tracking Visits
By default, the package enables tracking of all the available fields on each URL built. However, this can be toggled in
the config file.

##### Default Tracking
To disable tracking by default on all future short URLs that are generated, set the following in the config:
```
'tracking'   => [
        'default_enabled' => true,
        ...
]
```
Note: Disabling tracking by default won't disable tracking for any shortened URLs that already exist. It will only apply
to all shortened URLs that are created after the config update.

##### Tracking Fields
You can toggle the fields that are tracked for each visitor by changing them in the config.

For example, the snippet below shows how we could record all of the fields apart from the IP address of the visitor:

```
'tracking'   => [
        ...
        'fields' => [
            'ip_address'               => false,
            'operating_system'         => true,
            'operating_system_version' => true,
            'browser'                  => true,
            'browser_version'          => true,
        ],
    ],
```

Note: Updating the tracked fields will affect all existing and new shortened URLs.

### Helper Methods
#### Visits
The ShortURL model includes a relationship (that you can use just like any other Laravel model relation) for getting the
visits for a shortened URL.

To get the visits using the relationship, use ``` ->visits ``` or ``` ->visits() ```. The example snippet belows shows how:

```php
$shortURL = \AshAllenDesign\ShortURL\Models\ShortURL::find(1);
$visits = $shortURL->visits;
``` 
#### Find by URL Key
To find the ShortURL model that corresponds to a given shortened URL key, you can use the ``` ->findByKey() ``` method.

For example, to find the ShortURL model of a shortened URL that has the key ``` abc123 ```, you could use the following:
```php
$shortURL = \AshAllenDesign\ShortURL\Models\ShortURL::findByKey('abc123');
``` 

#### Find by Destination URL
To the ShortURL models that redirect to a given destination URL, you can use the ``` ->findByDestinationURL() ``` method.

For example, to find all of the ShortURL models of shortened URLs that redirect to ``` https://destination.com ```, you could use
the following:

```php
$shortURLs = \AshAllenDesign\ShortURL\Models\ShortURL::findByDestinationURL('https://destination.com');
```

## Testing

To run the package's unit tests, run the following command:

``` bash
vendor/bin/phpunit
```

## Security

If you find any security related, please contact me directly at [mail@ashallendesign.co.uk](mailto:mail@ashallendesign.co.uk) to report it.

## Contribution

If you wish to make any changes or improvements to the package, feel free to make a pull request.

Note: A contribution guide will be added soon.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
