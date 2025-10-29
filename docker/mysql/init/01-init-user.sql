-- Разрешить подключение пользователя yii2advanced с любого хоста
GRANT ALL PRIVILEGES ON yii2advanced.* TO 'yii2advanced'@'%' IDENTIFIED BY 'secret';
FLUSH PRIVILEGES;

-- Также разрешить подключение root с любого хоста (для удобства разработки)
GRANT ALL PRIVILEGES ON *.* TO 'root'@'%' IDENTIFIED BY 'verysecret' WITH GRANT OPTION;
FLUSH PRIVILEGES;

