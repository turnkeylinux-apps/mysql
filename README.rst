MySQL - Relational Database Management System
=============================================

`MySQL`_ is a well known relational database management system (RDBMS). It is
a fast, stable, robust, easy to use, and true multi-user, multi-threaded SQL
database server.

Strictly speaking, this appliance actually provides MariaDB_. MariaDB
is generally considered a `drop-in replacement for MySQL`_. However, it
does have `some features`_ that don't directly map to MySQL and the code bases
have drifted apart over time. As a general rule, it is possible to migrate from
MySQL to MariaDB, but often not possible to go back again.

This appliance includes all the standard features in `TurnKey Core`_,
and on top of that:

- MariaDB_ (MySQL replacement).
- Socket authentication for local 'root' MariaDB user. Secure and convenient.
  Access only from 'root' Linux user but no password required.
- Web Control Panel, including "quick start" docs, noting info about SSL
  MySQL connections.
- `Adminer`_ administration frontend for MySQL (listening on port
  12322 - uses SSL).
- MySQL webmin module (compatible with MariaDB).
- MySQLTuner_ - MariaDB tuning and review script. Provides insight and advice
  to optimize to your resources and use case.
- Dedicated remote MySQL/MariaDB user; 'remote'.
- MariaDB configured to listen on port 3306 TCP on all interfaces. SSL enabled
  (and required) by default.
  NOTE: In a production environment it is recommended to limit incoming
  connections to specific hosts::

    UPDATE `mysql`.`user` SET `Host` = 'hostname' 
    WHERE CONVERT( `user`.`Host` USING utf8 ) = '%' AND 
    CONVERT( `user`.`User` USING utf8 ) = 'remote' LIMIT 1 ;

Credentials *(passwords set at first boot)*
-------------------------------------------

-  Webmin, SSH, MySQL (terminal only): username **root**
-  Adminer: username **adminer**
-  MySQL (remote): username **remote**


.. _MySQL: https://www.mysql.com/
.. _MariaDB: https://mariadb.com/
.. _drop-in replacement for MySQL: https://mariadb.com/kb/en/mariadb-vs-mysql-compatibility/
.. _some features: https://mariadb.com/kb/en/mariadb-vs-mysql-compatibility/#drop-in-compatibility-of-specific-mariadb-versions
.. _TurnKey Core: https://www.turnkeylinux.org/core
.. _Adminer: https://adminer.org/
.. _MySQLTuner: https://github.com/major/MySQLTuner-perl/blob/master/README.md
