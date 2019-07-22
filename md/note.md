# 随手记

- 创建测试用户
```php
namespace App\Models
User::create(['name' => 'chendaye666','email'=>'chendaye666@gmail.com','mobile' => 15271834241,'password' => bcrypt('long')]);

namespace App\Models
Client::create(['name' => 'chendaye666','email'=>'chendaye666@gmail.com','mobile' => 15271834241,'password' => bcrypt('chendaye')]);
```
- fastdfs 错误

[tracker_query_storage fail, error no: 2, error info: No such file or directory](https://blog.csdn.net/xyw591238/article/details/51487736)

```bash
/usr/bin/restart.sh /usr/bin/fdfs_trackerd /etc/fdfs/tracker.conf \
&& /usr/bin/restart.sh /usr/bin/fdfs_storaged /etc/fdfs/storage.conf \
&& /usr/bin/fdfs_monitor /etc/fdfs/storage.conf \
&& /usr/bin/fdfs_upload_file /etc/fdfs/client.conf /usr/bin/fdfs_upload_file



VOLUME /etc/fdfs
VOLUME /home/dfs
docker-compose build fastdfs

解决：备份data改名 ， 重新build， 删掉新的data 恢复原data
```
