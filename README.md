Notification for Yii2
=====================
Notification for Yii2

[![Latest Stable Version](https://poser.pugx.org/yiier/yii2-notification/v/stable)](https://packagist.org/packages/yiier/yii2-notification) 
[![Total Downloads](https://poser.pugx.org/yiier/yii2-notification/downloads)](https://packagist.org/packages/yiier/yii2-notification) 
[![Latest Unstable Version](https://poser.pugx.org/yiier/yii2-notification/v/unstable)](https://packagist.org/packages/yiier/yii2-notification) 
[![License](https://poser.pugx.org/yiier/yii2-notification/license)](https://packagist.org/packages/yiier/yii2-notification)

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist yiier/yii2-notification "*"
```

or add

```
"yiier/yii2-notification": "*"
```

to the require section of your `composer.json` file.



Migrations
-----------

Run the following command

```shell
$ php yii migrate --migrationPath=@yiier/notification/migrations/
```

Usage
-----

**Config**

Configure Controller class as follows : :

```php
<?php
use yiier\notification\actions\NotificationAction;


class NotificationController extends Controller
{
    public function actions()
    {
        return [
            'do' => [
                'class' => NotificationAction::className(),
            ]
        ];
    }
}

```

**Url**

```
POST: http://xxxxxxxxxxxxxx/notification/do
Form Data: action=read_all

POST: http://xxxxxxxxxxxxxx/notification/do
Form Data: action=read_all&ids=1,2,3

POST: http://xxxxxxxxxxxxxx/notification/do
Form Data: action=delete_all

POST: http://xxxxxxxxxxxxxx/notification/do
Form Data: action=delete_all&ids=1,2,3

POST: http://xxxxxxxxxxxxxx/notification/do
Form Data: action=delete&id=1
```

`action=delete` you can use `action=Notification::DEL_ACTION`


http response success(code==200) return json:

```json
{"code":200,"data":0,"message":"success"}
```
http response failure(code==500) return json:

```json
{"code":500,"data":"","message":"xxxx"}
```

More [Notification](/src/models/Notification.php)