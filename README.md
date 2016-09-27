Symfony 2.8 + Sonata Admin 2.3 Boilerplate
================

This is a boilerplate I've made to gain time when I need to kickstart projects

This ultimate symfony2 boilerplate comes with :

* [FOSUserBundle](https://github.com/FriendsOfSymfony/FOSUserBundle) : Provides user management for your Symfony2 Project. Compatible with Doctrine ORM & ODM, and Propel.


## Installation

This boilerplate comes with all the Sonata bundles enabled and preconfigured

The easiest way to get started is to clone the repository:

```bash
$ mkdir myproject
$ cd myproject
# Get the latest snapshot
$ git clone https://github.com/AlexWoroschilow/symfony-sonata-admin.git ./
$ git remote rm origin
$ make
$ make install
# A superadmin user is created with the fixtures with username `admin` and password `admin`

$ php app/console server:run
