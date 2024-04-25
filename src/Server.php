<?php

namespace Yggverse\Nps;

class Server
{
    private string $_host;
    private int    $_port;
    private int    $_size;
    private int    $_line;
    private bool   $_live;

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

    public function start(
        ?callable $handler = null
    ): void
    {
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

        do
        {
            if (!$this->_live)
            {
                fclose(
                    $socket
                );

                break;
            }

            $incoming = stream_socket_accept(
                $socket, -1, $connect
            );

            stream_set_blocking(
                $incoming,
                true
            );

            $request = fread(
                $incoming,
                $this->_line
            );

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

            fclose(
                $incoming
            );

            if ($handler)
            {
                $response = call_user_func(
                    $handler,
                    $success,
                    $content,
                    $request,
                    $connect
                );
            }

        } while ($this->_live);
    }

    public function stop(): void
    {
        $this->setLive(
            false
        );
    }
}
