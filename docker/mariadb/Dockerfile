FROM mariadb:10.5-focal

HEALTHCHECK --interval=1s --retries=10 CMD mysqladmin ping --silent || exit 1
