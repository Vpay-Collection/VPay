# Tasker服务

    Tasker服务是一种基于nginx多线程原理的定时任务工具。他通过后台重新拉起一个http后台连接来启动tasker服务，该服务通过10s轮询sqlite数据库实现定时任务执行。

# 文件说明
 
   - data/db.yml 拓展所需的数据库信息，默认使用的sqlite3，可以自行修改配置文件使用mysql之类的
   - data/data.db sqlite3数据库文件
   - core/Tasker.php 定时任务类
   - core/Async.php 后台服务类
   
# 基本信息

- Ver 1.0
- Powered by Ankio
