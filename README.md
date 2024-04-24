# nps-php

PHP 8 / Composer Library for NPS Protocol (see also [nex-php](https://github.com/YGGverse/nex-php))

Like Titan for Gemini, NPS is the satellite for Nex protocol\
it uses dot in line to signal the package ending.

**Example**

1. `nc nightfall.city 1915` - connect server using `nc`
2. `classifieds` - enter the target path
3. `YOUR MESSAGE GOES HERE` - enter the message text
4. `.` - commit package with dot

To send any file:

``` file.txt
classifieds
╦ ╦╔═╗╔═╗╔╦╗╦═╗╔═╗╔═╗╦╦
╚╦╝║ ╦║ ╦ ║║╠╦╝╠═╣╚═╗║║
 ╩ ╚═╝╚═╝═╩╝╩╚═╩ ╩╚═╝╩╩═╝
.
```

`cat file.txt | nc nightfall.city 1915`

**Specification**

`nex://nightfall.city/nps/`

## Usage

```
composer require yggverse/nps
```

## Server

Build interactive server instance to listen NPS protocol connections!

``` php
$server = new \Yggverse\Nps\Server;
```

Provide optional `host`, `port` and `size` arguments in constructor or use available setters after object initiation.

``` php
$server = new \Yggverse\Nps\Server('127.0.0.1', 1915);
```

#### Server::setHost
#### Server::getHost
#### Server::setPort
#### Server::getPort
#### Server::setSize
#### Server::getSize
#### Server::setLive
#### Server::getLive

#### Server::start

Run server object using this method.

Define handler function as the argument to process application logic dependent of client request.

``` php
$server->start(
    function (
        string $request,
        string $connect
    ) {
        printf(
            'connection: %s request: %s',
            $connect,
            $request
        );
    }
);
```

#### Server::stop

Stop server instance.

Same to `Server::setLive(false)`
