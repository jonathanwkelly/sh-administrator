# Admin Architect

Admin Architect is a framework for creating administration interfaces.
It abstracts common business application patterns to make it simple for developers to implement beautiful and elegant interfaces with very little effort.


# Installation

There are 2 ways to install Admin Architect: via gitlab repository (only for private members) and by downloading zip archive.

## Via Gitlab repository (Private members)

Add a new repository to your composer.json

```
"repositories": [
  ...
  {
    "type": "git",
    "url": "git@gitlab.top.md:terranet/administrator.git"
  }
  ...
]
```

## Via Zip archive (Public way)

Downlad the [latest](http://codecanyon.net/item/laravel-admin-administration-framework/13528564) version of Admin Architect and extract this archive anywere in your project, for instance ```packages``` directory.

Add a new repository to your composer.json

```
"repositories": [
  ...
  {
    "type": "git",
    "url": "./packages/administrator"
  }
  ...
]
```

Since our repository type is git, let's init a new repository

Enter to ```packages/administrator``` directory and run:
  
```
git init;
git add .
git commit -m 'First init'
```

## Install package

```
composer require terranet/administrator
```

After the package installed, register its service provider to the providers array in config/app.php file.

```
'providers' => [
    ...
    Terranet\Administrator\ServiceProvider::class
    ...
]
```

Then, publish package's assets by running: 

```
artisan vendor:publish
```
 
OR if you want to publish only administrator's files.

```
artisan vendor:publish --provider=Terranet\\Administrator\\ServiceProvider
``` 

### Laravel 5.2 version (Admin Architect 3.x)

if you use Admin Architect 3.x for Laravel 5.2 there is an optional step you may need to configure.
Since Laravel 5.2 was updated authentication module, by default Admin Architect declares new `guard` called 'admin' and new `provider` 'admins' that uses User model

So if you want to handle the authentication in other way, just redeclare the `providers` section in `config/auth.php` config file.
for example: 

```
'providers' => [
	...
	'admins' => [
    	'driver' => 'eloquent',
    	'model'  => Admin::class,
  	],
  	...
]
```

### Laravel 5.1 version (Admin Architect 2.x)

* set the desired admin model: `config/administrator.php - auth_model` section, 
* modify `permissions` section according to your needs
* Optionaly (depending on changes in previous step), add a method <AdminModel>::isSuperAdmin() which will be used do determine if current logged has permissions to enter in administration area.

## Create administrator

Now you're ready to create a new administrator

```
artisan administrator:create
```

*(Admin Architect will take care of your administrator model class.)*

Admin Architect needs almost no other configuration out of the box. You are free to get started developing! 
However, you may wish to review the config/administrator.php file and its contents. It contains several options that you may wish to change according to your application.