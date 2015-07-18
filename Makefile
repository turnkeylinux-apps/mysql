WEBMIN_FW_TCP_INCOMING = 22 80 443 3306 12320 12321

COMMON_OVERLAYS = adminer tkl-webcp
COMMON_CONF = adminer-lighttpd adminer-mysql tkl-webcp

include $(FAB_PATH)/common/mk/turnkey/php.mk
include $(FAB_PATH)/common/mk/turnkey/mysql.mk
include $(FAB_PATH)/common/mk/turnkey.mk
