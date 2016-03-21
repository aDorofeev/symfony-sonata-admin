Symfony 2.8 + Sonata Admin 2.3 Boilerplate
================

This is a boilerplate I've made to gain time when I need to kickstart projects

This ultimate symfony2 boilerplate comes with :

* [FOSUserBundle](https://github.com/FriendsOfSymfony/FOSUserBundle) : Provides user management for your Symfony2 Project. Compatible with Doctrine ORM & ODM, and Propel.

<!-- -->

## Installation

This boilerplate comes with all the Sonata bundles enabled and preconfigured

The easiest way to get started is to clone the repository:

```bash
# Get the latest snapshot
$ mkdir myproject
$ cd myproject
$ git clone https://github.com/numerogeek/symfony-sonata-admin ./
$ git remote rm origin

#setup ACL (refer to the symfony documentation.
$ HTTPDUSER=`ps aux | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx' | grep -v root | head -1 | cut -d\  -f1`
$ sudo setfacl -R -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX app/cache app/logs
$ sudo setfacl -dR -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX app/cache app/logs

make
make install

# A superadmin user is created with the fixtures with username `admin` and password `admin`



```

## Starter Kit

Go to http://www.myproject.local/app_dev.php/admin to access the admin and refer the sonata documentation to create new admin panel.
