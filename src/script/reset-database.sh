cd /home/lojze/hacks/code/bdpa/tpo/doc/baza
echo "drop database tpo; create database tpo" | mysql -utpo -ptpo tpo; mysql -utpo -ptpo tpo < crebas.sql; mysql -utpo -ptpo tpo < insert-md5-periodic.sql
