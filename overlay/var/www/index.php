<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">

<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <meta http-equiv="Content-Style-Type" content="text/css">
        <meta http-equiv="Content-Script-Type" content="text/javascript">

        <title>TurnKey MySQL</title>
        
        <link rel="stylesheet" href="css/ui.tabs.css" type="text/css" media="print, projection, screen">
        <link rel="stylesheet" href="css/base.css" type="text/css">

        <script src="js/jquery-1.2.6.js" type="text/javascript"></script>
        <script src="js/ui.core.js" type="text/javascript"></script>
        <script src="js/ui.tabs.js" type="text/javascript"></script>
        <script type="text/javascript">
            $(function() {
                $('#container-1 > ul').tabs({ fx: { opacity: 'toggle'} });
            });
        </script>
    </head>

    <body>
        <h1>TurnKey MySQL</h1>
        
        <div id="container-1">
            <ul>
                <li><a href="#cp"><span>Control Panel</span></a></li>
                <li><a href="#docs"><span>Connection Docs</span></a></li>
            </ul>

            <div id="cp">
                <div class="fragment-content">
                    <div>
                        <a href="https://<?php print
                        $_SERVER['HTTP_HOST']; ?>:12321"><img
                        src="images/webmin.png"/>Webmin</a>
                    </div>
                    <div>
                        <a href="https://<?php print
                        $_SERVER['HTTP_HOST']; ?>:12322"><img
                        src="images/adminer.png"/>Adminer</a>
                    </div>
                    <div></div>
                    <div></div>
                    <div></div>

                    <h2>Resources and references</h2>
                    <ul>
                        <li><a
                        href="https://www.turnkeylinux.org/mysql">
                        TurnKey MySQL release notes</a></li>
                        <li>Connection Docs tab (above) has connection info (or see <a
                        href="https://github.com/turnkeylinux-apps/mysql/tree/master/docs">
                        online</a>)</li>
                    </ul>

                </div>
            </div>
            <div id="docs">
                <div class="fragment-content">
                    <h2>Connection Hints</h2>

                    <p>As per all previous releases, by default TurnKey MySQL (MariaDB) appliance
                    listens on all interfaces via (default MySQL/MariaDB) port
                    <strong>3306</strong>.</p>

                    <p>However, since v16.0 there have been some changes...</p>

                    <h3>New remote user username</h3>
                    As of v16.0+ the default "root-like" user is now named
                    "<strong>remote</strong>".

                    <h3>SSL now enabled (and required) for the "remote" user</h3>

                    <p>SSL is now enabled and required for remote TCP connections to the
                    MySQL/MariaDB server. If desired it can be disabled (and re-enabled) via the
                    Confconsole plugin (Advanced &gt;&gt; System Settings &gt;&gt; MySQL remote
                    SSL) and/or the 'turnkey-mysql-ssl' commandline tool.</p>

                    <h3>SSL details</h3>

                    <p>Self-signed certificates, signed by a custom CA cert are all generated on
                    firstboot and stored in '/etc/mysql/certificates'. To connect remotely via SSL,
                    you will need to download the relevant files and configure your client to use
                    these, or reconfigure it to your desires. The required files are:</p>

                    <pre>
/etc/mysql/certificates/ca.pem # The CA certifcate
/etc/mysql/certificates/cert.pem # The certificate file
/etc/mysql/certificates/cert.key # The key file</pre>

                    <p>For example, to use the commandline MySQL/MariaDB client from another
                    TurnKey instance, assuming that the files have been downloaded to the same
                    local locations, the following lines are required in the MySQL/MariaDB client
                    config ('/etc/mysql/mariadb.conf.d/50-client.cnf'):</p>

                    <pre>
ssl_ca = /etc/mysql/certificates/ca.pem
ssl-cert = /etc/mysql/certificates/cert.pem
ssl-key = /etc/mysql/certificates/cert.key</pre>

                    <p>Note that the user who is launching the client must have read permission for
                    these files.</p>

                    <p>Once configured, then connection should work as per usual remote
                    MySQL/MariaDB connection. E.g.:</p>

                    <pre>
root@core ~# mysql -h remote-mysql.example.com -u remote -p
Enter password:
Welcome to the MariaDB monitor.  Commands end with ; or \g.
Your MariaDB connection id is 41
Server version: 10.3.22-MariaDB-0+deb10u1 Debian 10

Copyright (c) 2000, 2018, Oracle, MariaDB Corporation Ab and others.

Type 'help;' or '\h' for help. Type '\c' to clear the current input statement.

MariaDB [(none)]></pre>

                    <p>Then to demonstrate that the connection is encrypted, you can use the '\s'
                    command. I.e.:</p>

                    <pre>
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
--------------</pre>

                <p>Note the ciper noted against "SSL:"! :)</p>

                <h3>Alternate configurations</h3>
                There are a number of alternate configrations possible (including using "proper" CA
                signed certs) but you are on your own with those for now. Please see the <a
                href="https://mariadb.com/kb/en/securing-connections-for-client-and-server/">
                MariaDB "Securing Connections" KB page</a> for further ideas.<p>

                <p>If you do configure this appliance to connect via SSL in alternate way and would
                like to share your config (please do!), and/or have any questions please feel free
                to post in the <a href="https://www.turnkeylinux.org/forum">TurnKey forums</a>.</p>

                <p>For the most up to date details, please check the <a
                href="https://www.turnkeylinux.org/mysql">MySQL appliance page</a> and/or the <a
                href="https://github.com/turnkeylinux-apps/mysql/tree/master/docs">docs</a>.</p>
                </div>
            </div>

        </div>
    </body>
</html>
