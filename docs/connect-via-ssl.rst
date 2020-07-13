Connect to MySQL/MariaDB remotely
=================================

As per all previous releases, by default TurnKey MySQL (MariaDB) appliance
listens on all interfaces via (default MySQL/MariaDB) port **3306**.

However, since v16.0 there have been some changes...

New remote user username
------------------------

As of v16.0+ the default "root-like" user is now named "remote".

SSL now enabled (and required) for the "remote" user
----------------------------------------------------

The SSL requirement is enabled and required for remote connections. If desired
it can be disabled via the Confconsole plugin (Advanced >> System Settings
>> MySQL remote SSL) or the 'turnkey-mysql-ssl' commandline tool.

SSL details
-----------

Self-signed certificates, signed by a custom CA cert are all generated on
firstboot and stored in '/etc/mysql/certificates'. To connect remotely via SSL,
you will need to download the relevant files and configure your client to use
these, or reconfigure it to your desires. The required files are::

   /etc/mysql/certificates/ca.pem # The CA certifcate
   /etc/mysql/certificates/cert.pem # The certificate file
   /etc/mysql/certificates/cert.key # The key file

For example, to use the commandline MySQL/MariaDB client from another TurnKey
instance, assuming that the files have been downloaded to the same local
locations, the following lines are required in the MySQL/MariaDB client config
('/etc/mysql/mariadb.conf.d/50-client.cnf')::

   ssl_ca = /etc/mysql/certificates/ca.pem
   ssl-cert = /etc/mysql/certificates/cert.pem
   ssl-key = /etc/mysql/certificates/cert.key

Note that the user who is launching the client must have read permission for
these files.

Once configured, then connection should work as per usual remote MySQL/MariaDB
connection. E.g.::

   root@core ~# mysql -h remote-mysql.example.com -u remote -p
   Enter password:
   Welcome to the MariaDB monitor.  Commands end with ; or \g.
   Your MariaDB connection id is 41
   Server version: 10.3.22-MariaDB-0+deb10u1 Debian 10

   Copyright (c) 2000, 2018, Oracle, MariaDB Corporation Ab and others.

   Type 'help;' or '\h' for help. Type '\c' to clear the current input statement.

   MariaDB [(none)]>

Then to demonstrate that the connection is encrypted, you can use the '\s'
command. I.e.::

   MariaDB [(none)]> \s
   --------------
   mysql  Ver 15.1 Distrib 10.3.22-MariaDB, for debian-linux-gnu (x86_64) using readline 5.2

   Connection id:		41
   Current database:
   Current user:		remote@remote-mysql.example.com
   SSL:			Cipher in use is DHE-RSA-AES256-SHA
   Current pager:		less -X -R -F
   Using outfile:		''
   Using delimiter:	;
   Server:			MariaDB
   Server version:		10.3.22-MariaDB-0+deb10u1 Debian 10
   Protocol version:	10
   Connection:		192.168.1.74 via TCP/IP
   Server characterset:	utf8mb4
   Db     characterset:	utf8mb4
   Client characterset:	utf8mb4
   Conn.  characterset:	utf8mb4
   TCP port:		3306
   Uptime:			34 min 12 sec

   Threads: 7  Questions: 77  Slow queries: 0  Opens: 32  Flush tables: 1  Open tables: 26  Queries per second avg: 0.037
   --------------

Note the ciper noted against "SSL:"! :)

Alternate configurations
------------------------

There are a number of alternate configurations possible (including using
"proper" CA signed certs) but you are on your own with those for now.
Please see the `MariaDB "Securing Connections" KB page`_ for further ideas.

If you do configure this appliance to connect via SSL in alternate way and
would like to share your config (please do!), and/or have any questions
please feel free to post in the `TurnKey forums`_.

For the most up to date details, please check the `MySQL appliance page`_
and/or the `docs`_.

.. _MariaDB "Securing Connections" KB page: https://mariadb.com/kb/en/securing-connections-for-client-and-server/
.. _TurnKey forums: https://www.turnkeylinux.org/forum
.. _MySQL appliance page: https://www.turnkeylinux.org/mysql
.. _docs: https://github.com/turnkeylinux-apps/mysql/tree/master/docs
