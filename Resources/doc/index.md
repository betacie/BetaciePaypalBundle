Getting started
===============

Now that you have installed the bundle, you will be able to use it.

Configuration
-------------

First of all, you must defined your paypal credentials.

```yaml
# app/config/config.yml

betacie_paypal:
    credentials:
        user: user              # As defined in your dashboard
        password: password      # As defined in your dashboard
        signature: signature    # As defined in your dashboard
    debug: true                 # true or false
```

You'll notice that you must specify a debug value, so the endpoint API will not be the same. 
With debug defined as `true` you will call paypal sandbox.

Routing
-------

Now you must load routing:
```yaml
# app/config/routing.yml

betacie_paypal:
    resource: "@BetaciePaypalBundle/Resources/config/routing.xml"
    prefix:   /paypal
```

It is recommended to use the prefix to avoid conflicts.

Database
--------

As mentionned in introduction, your project must used Doctrine to manage database. Now you must run

```bash
$ php app/console doctrine:schema:update --force
```

How to use ?
------------

[Checkout Express](checkout.md)
[Mass payment](masspay.md)