MySQL - Relational Database Management System
=============================================

`MySQL`_ is a relational database management system (RDBMS) which has
more than 11 million installations, and is owned by Oracle.
MySQL is a fast, stable, robust, easy to use, and true multi-user,
multi-threaded SQL database server.

This appliance includes all the standard features in `TurnKey Core`_,
and on top of that:

- Web Control Panel
- `Adminer`_ administration frontend for MySQL (listening on port
  12322 - uses SSL).
- MySQL webmin module.
- MySQL is configured to listen on port 3306 TCP on all interfaces by
  default.
- For convenience MySQL is configured to accept connections from all
  hosts by default. In a production environment it is recommended to
  limit incoming connections to specific hosts::

    UPDATE `mysql`.`user` SET `Host` = 'hostname' 
    WHERE CONVERT( `user`.`Host` USING utf8 ) = '%' AND 
    CONVERT( `user`.`User` USING utf8 ) = 'root' LIMIT 1 ;

Note: MySQL can be further tweaked to optimize performance, for examples
refer to: */usr/share/doc/mysql-server-5.1/examples/{small, medium,
large, huge}*

Credentials *(passwords set at first boot)*
-------------------------------------------

-  Webmin, SSH, MySQL, Adminer: username **root**


.. _MySQL: http://www.mysql.com/
.. _TurnKey Core: http://www.turnkeylinux.org/core
.. _Adminer: http://adminer.org/
