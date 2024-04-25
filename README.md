# nps-php

PHP 8 / Composer Library for NPS Protocol

Like Titan for Gemini, NPS is the satellite for NEX protocol (see also [nex-php](https://github.com/YGGverse/nex-php))\
it listen for single dot in line to signal the package ending.

## Specification

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

Provide optional `host`, `port`, `size`, `line` and `live` arguments in constructor:

``` php
$server = new \Yggverse\Nps\Server('127.0.0.1', 1915);
```

Alternatively, just use available setters documented bellow after object initiation

#### Server::setHost

Bind server host to listen incoming connections, `127.0.0.1` by default

#### Server::getHost

Get current server host

#### Server::setPort

Bind server port to listen incoming connections, `1915` by default

#### Server::getPort

Get current server port

#### Server::setSize

Set total content length limit by [mb_strlen](https://www.php.net/manual/en/function.mb-strlen.php), `0` by default (unlimited)

#### Server::getSize

Get current content length limit

#### Server::setLine

Set packet line limit in bytes passing to [fread](https://www.php.net/manual/en/function.fread.php#length), `1024` by default

#### Server::getLine

Get current packet line limit

#### Server::setLive

Set server status `true`|`false` to shutdown immediately

#### Server::getLive

Get current server status

#### Server::start

Run server object using this method.

Define handler function as the argument to process application logic dependent of client request.

``` php
$server->start(
    function (
          bool $success,
        string $content,
        string $request,
        string $connect
    ) {
        printf(
            'connection: %s request: %s',
            $connect,
            $request
        );

        if ($success)
        {
            var_dump(
                $content
            );
        }
    }
);
```

#### Server::stop

Stop server instance.

Same to `Server::setLive(false)`

### Testing

1. `nc 127.0.0.1 1915` - connect server using `nc`
2. `test` - enter the target path
3. `YOUR MESSAGE GOES HERE` - enter the message text
4. `.` - commit package with dot

To send any file:

``` file.txt
test
╦ ╦╔═╗╔═╗╔╦╗╦═╗╔═╗╔═╗╦╦
╚╦╝║ ╦║ ╦ ║║╠╦╝╠═╣╚═╗║║
 ╩ ╚═╝╚═╝═╩╝╩╚═╩ ╩╚═╝╩╩═╝
.
```

`cat file.txt | nc 127.0.0.1 1915`