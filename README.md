# Weather
搜集城市天气预报信息，存入 mysql <br />
collect weather information in China, and save to mysql


    - onn.php 数据库连接信息及API配置信息
    - api.php  调用天气接口，获取数据
    - dao.php	数据库操作
    - weather.php 程序入口
    - errors.log log文件，需要读写权限
    - weather.sql 数据库

程序逻辑：

从Weather数据库中的city表获取城市列表，然后查询每个城市天气存储到数据库中（表名为城市名）