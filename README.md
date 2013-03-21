paypal-bundle
=============

This bundle provides methods for using the paypal API

Features include:
* Express Checkout
* Mass payment

Prerequisites
-------------
You must use Doctrine as database management, nor Propel neither MongoDB are supported.

Installation (with composer)
----------------------------

Add BetaciePaypalBundle in your composer.json

```js
{
        "require": {
                "betacie/paypal-bundle": "dev-master"
        },
        "repositories": [
                {
                        "type": "git",
                        "url": "git@github.com:betacie/BetaciePaypalBundle.git"
                }
        ]
}
```

Now download the bundle:

```bash
$ php composer.phar update betacie/paypal-bundle
```

Now that BetaciePaypalBundle has been downloaded you can enable him in the kernel:

```php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Betacie\Bundle\PaypalBundle\BetaciePaypalBundle(),
    );
}
```

That's it!

Documentation
-------------
You can find a complete documentation in `Resources/doc` :
[Getting started](https://github.com/betacie/BetaciePaypalBundle/tree/master/Resources/doc/index.md)