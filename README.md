Для получения локального ipv4 адреса:
```shell
ifconfig | grep 192
```

На бою поменять:
ws://192.168.2.42:8080
на
wss://%ip_server_address%:8080
и включить проксирование nginx

Ссылка на прокси nginx:
https://nginx.org/en/docs/http/websocket.html

Execute with supervisor
```
[program:websocket]
autostart=true
autorestart=true
command=php /var/www/project_path/artisan websocket:run
startretries=3
```

```
supervisorctl
```
> help (stop|start|restart)
