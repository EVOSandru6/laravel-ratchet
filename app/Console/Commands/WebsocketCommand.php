<?php

namespace App\Console\Commands;

use App\Helpers\Websocket;
use Illuminate\Console\Command;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;

class WebsocketCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'websocket:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to server start';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('websocket started!');

        $server = IoServer::factory(
            new HttpServer(
                new WsServer(
                    new Websocket()
                )
            ), 8080
        );

        $server->run();
    }
}
