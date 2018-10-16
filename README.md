ImagesParser
============

A Symfony project created on October 13, 2018, 7:58 pm.

Install vendors and confirm parameters.

```sh
$ php compose.phar install
```

Edit /etc/hosts
```sh
$ sudo nano /etc/hosts
>> 127.0.0.1 url-parser.local
```

Next run docker
```sh
$ docker-compose up -d
```

Connect to docker php image and accept migrations
```sh
$ docker-compose exec php bash
$ php bin/console doctrine:migrations:migrate
```

Try to exec parse command from docker php image.
```sh
$ php bin/console parse:link 'http://gadget-it.ru/' 10 1
```
Check while command finished
```sh
$ Parse finished
```

Open "url-parser.local"

If get http code 500 - try remove cache
```sh
$ docker-compose exec php bash
$ rm -rf var/cache/*
```