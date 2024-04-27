<?php

namespace Yggverse\Nps;

class Server
{
    private string $_host;
    private int    $_port;
    private int    $_size;
    private int    $_line;
    private bool   $_live;

    private $_welcome = null;
    private $_pending = null;
    private $_handler = null;

    public function __construct(
        string $host = '127.0.0.1',
        int    $port = 1915,
        int    $size = 0,
        int    $line = 1024,
        bool   $live = true
    ) {
        $this->setHost(
            $host
        );

        $this->setPort(
            $port
        );

        $this->setSize(
            $size
        );

        $this->setLine(
            $line
        );

        $this->setLive(
            $live
        );
    }

    public function getHost(): string
    {
        return $this->_host;
    }

    public function setHost(
        string $value
    ): void
    {
        if (false === filter_var($value, FILTER_VALIDATE_IP))
        {
            throw new \Exception();
        }

        if (strpos($value, ':'))
        {
            $value = sprintf(
                '[%s]',
                $value
            );
        }

        $this->_host = $value;
    }

    public function getPort(): int
    {
        return $this->_port;
    }

    public function setPort(
        int $value
    ): void
    {
        $this->_port = $value;
    }

    public function getSize(): int
    {
        return $this->_size;
    }

    public function setSize(
        int $value
    ): void
    {
        $this->_size = $value;
    }

    public function getLine(): int
    {
        return $this->_line;
    }

    public function setLine(
        int $value
    ): void
    {
        $this->_line = $value;
    }

    public function getLive(): bool
    {
        return $this->_live;
    }

    public function setLive(
        bool $value
    ): void
    {
        $this->_live = $value;
    }

    public function getWelcome(): callable
    {
        return $this->_welcome;
    }

    public function setWelcome(
        callable $function
    ): void
    {
        $this->_welcome = $function;
    }

    public function getPending(): callable
    {
        return $this->_pending;
    }

    public function setPending(
        callable $function
    ): void
    {
        $this->_pending = $function;
    }

    public function getHandler(): callable
    {
        return $this->_handler;
    }

    public function setHandler(
        callable $function
    ): void
    {
        $this->_handler = $function;
    }

    public function start(
        ?callable $handler = null
    ): void
    {
        if ($handler)
        {
            $this->setHandler(
                $handler
            );
        }

        $socket = stream_socket_server(
            sprintf(
                'tcp://%s:%d',
                $this->_host,
                $this->_port
            ),
            $error,
            $message,
            STREAM_SERVER_BIND | STREAM_SERVER_LISTEN
        );

        if ($this->_live)
        {
            $this->_live = is_resource(
                $socket
            );
        }

        do
        {
            if (!$this->_live)
            {
                break;
            }

            $incoming = stream_socket_accept(
                $socket, -1, $connect
            );

            if ($this->_welcome)
            {
                $response = call_user_func(
                    $this->_welcome,
                    $connect
                );

                if ($response)
                {
                    fwrite(
                        $incoming,
                        $response
                    );
                }
            }

            stream_set_blocking(
                $incoming,
                true
            );

            $request = fread(
                $incoming,
                $this->_line
            );

            if ($this->_pending)
            {
                $response = call_user_func(
                    $this->_pending,
                    $request,
                    $connect
                );

                if ($response)
                {
                    fwrite(
                        $incoming,
                        $response
                    );
                }
            }

            $success = true;

            $content = '';

            do
            {
                $line = trim(
                    fread(
                        $incoming,
                        $this->_line
                    )
                );

                if ($this->_size && mb_strlen($content) > $this->_size)
                {
                    $success = false;

                    break;
                }

                if ($line == '.')
                {
                    break;
                }

                $content .= $line;

            } while ($this->_live);

            stream_set_blocking(
                $incoming,
                false
            );

            if ($this->_handler)
            {
                $response = call_user_func(
                    $this->_handler,
                    $success,
                    $content,
                    $request,
                    $connect
                );

                if ($response)
                {
                    fwrite(
                        $incoming,
                        $response
                    );
                }
            }

            fclose(
                $incoming
            );

        } while ($this->_live);

        if (is_resource($socket))
        {
            fclose(
                $socket
            );
        }
    }

    public function stop(): void
    {
        $this->setLive(
            false
        );
    }
}
