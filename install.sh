#!/bin/bash

# variables
mysqlrootpw="mysqlrootpw"
mysqltodou="todouser"
mysqltodopw="todopw"
mysqldb="todo"

# install dependences
apt-get -y install apache2 libapache2-mod-php5 mysql-server-5.5 php5-mysql mailutils sendmail
# delete default hello world
rm /var/www/index.html

# restart because of php5-mysql
/etc/init.d/apache2 restart

# root database user
echo "CREATE USER '$mysqltodou'@'%' IDENTIFIED BY '$mysqltodopw';" | mysql -u root -p$mysqlrootpw
echo "CREATE USER '$mysqltodou'@'localhost' IDENTIFIED BY '$mysqltodopw';" | mysql -u root -p$mysqlrootpw
echo "create database $mysqldb;" | mysql -u root -p$mysqlrootpw
echo "GRANT ALL PRIVILEGES ON $mysqldb.* TO $mysqltodou@'%';" | mysql -u root -p$mysqlrootpw
####

# create database tables
mysql -u$mysqltodou -p$mysqltodopw $mysqldb < doc/baza/crebas.sql

# copy data to the web
cp -r src/web/* /var/www

# copy scripts to /opt
mkdir /opt/todo
cp src/script/* /opt/todo

# correct credentials
cat /var/www/php/include/const.php | sed "s/define(\"DB_USER\", \"tpo\");/define(\"DB_USER\", \"$mysqltodou\");/g">/tmp/a
cat /tmp/a >/var/www/php/include/const.php
cat /var/www/php/include/const.php | sed "s/define(\"DB_PASS\", \"tpo\");/define(\"DB_PASS\", \"$mysqltodopw\");/g">/tmp/a
cat /tmp/a >/var/www/php/include/const.php
cat /var/www/php/include/const.php | sed "s/define(\"DB_NAME\", \"tpo\");/define(\"DB_NAME\", \"$mysqldb\");/g">/tmp/a
cat /tmp/a >/var/www/php/include/const.php
rm /tmp/a

# add crontabs for periodic task checking and reminders
echo "*/5 * * * * www-data /usr/bin/php /opt/todo/send_reminders.php >> /var/log/todo_reminders 2>&1" >/etc/cron.d/todo
echo "*/5 * * * * www-data /usr/bin/php /opt/todo/periodic_tasks.php >> /var/log/todo_periodic 2>&1" >> /etc/cron.d/todo
/etc/init.d/cron restart

# create logging files and add proper permissions
touch /var/log/todo_periodic 
touch /var/log/todo_reminders
chown www-data:www-data /var/log/todo_periodic
chown www-data:www-data /var/log/todo_reminders

# add logrotate entry
cat > /etc/logrotate.d/todo <<END
/var/log/todo_periodic
/var/log/todo_reminders
{
        daily
        missingok
        rotate 52
        compress
        delaycompress
        notifempty
        create 640 www-data www-data
        sharedscripts
}
END

