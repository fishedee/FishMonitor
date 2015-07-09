# FishMonitor
使用PHP实现的系统监控工具，使用阿里云的云监控作为监控UI
可监控nginx,hhvm,codeigniter等服务和框架的运行状况。

#安装
```
composer install
```
下载源代码，然后在源代码目录输入以上命令即可安装相应的PHP依赖。

#配置
##本地配置
打开config.json的文件，配置对应的网络端口，以及云监控的用户名。
##阿里云配置
并且将需要监控的服务的字段注册到阿里云监控中，注意，所有的监控服务都是1分钟的监控频率。

#启动
```
sudo php index.php start -d -c config.json
```
即可启动监控脚本，开始享受你的监控吧～
