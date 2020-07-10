"""Enable/Disable SSL for MySQL remote connections"""

import subprocess
import os

SCRIPT_PATH = '/usr/local/bin/turnkey-mysql-ssl'


def set_ssl(set_status):
    subprocess.check_output([SCRIPT_PATH, set_status])


def check_status():
    if os.path.isfile(SCRIPT_PATH) and os.access(SCRIPT_PATH, os.X_OK):
        exit_code = subprocess.run([SCRIPT_PATH, 'status']).returncode
        if exit_code == 0:
            return 'enabled'
        else:
            return 'disabled'
    else:
        return False


def run():
    status = check_status()
    if not status:
        msg = ('The script to toggle SSL for MySQL remote connections is either missing or not executable.\n'
               'Please investigate or report this issue.')
        r = console.msgbox('Error', msg)
    else:
        msg = '''Automatic certificate renewal is currently {}'''
        r = console._wrapper('yesno', msg.format(status), 10, 30,
                             yes_label='Toggle', no_label='Ok')
        while r == 'ok':
            if status == 'enabled':
                set_ssl('disable')
            else:
                set_ssl('enable')
            status = check_status()
            r = console._wrapper('yesno', msg.format(status), 10, 30,
                                 yes_label='Toggle', no_label='Ok')
