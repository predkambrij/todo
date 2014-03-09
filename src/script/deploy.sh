#!/bin/bash

LANG=en_GB.UTF-8
LANGUAGE=en_GB:en
pwd
# safetly
cd /var/www/tododeploy

# do full backup before
mysqldump -utpo -ptpo tpo > tpo-full-backup.sql
cp -r ../todo todo-full-backup

# replace code with latest from svn
rm -rf ../todo
svn --username 'lojze' --password 'lojze1' export http://blatnik.org/svn/tpo
mv tpo ../todo
chown -R www-data:www-data ../todo

# update database
mysqldump --no-create-db --no-create-info --complete-insert -utpo -ptpo tpo > tpo1.sql
echo "drop database tpo;" | mysql -utpo -ptpo
echo "create database tpo;" | mysql -utpo -ptpo
mysql -utpo -ptpo tpo < ../todo/doc/baza/crebas.sql
mysql -utpo -ptpo tpo < tpo1.sql


echo finish
