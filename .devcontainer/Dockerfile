FROM mcr.microsoft.com/devcontainers/base:ubuntu

RUN apt-get update && export DEBIAN_FRONTEND=noninteractive \
    && apt-get -y install --no-install-recommends mariadb-client \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* \
    && echo "[client]" > /home/vscode/.my.cnf \
    && echo "user=root" >> /home/vscode/.my.cnf \
    && echo "password=root" >> /home/vscode/.my.cnf \
    && echo "host=db" >> /home/vscode/.my.cnf \
    && chown vscode /home/vscode/.my.cnf
