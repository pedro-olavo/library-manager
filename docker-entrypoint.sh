#!/bin/bash
#
# Entrypoint da aplicação.
#
# Em alguns ambientes de build/deploy, a imagem base php:8.2-apache chega
# ao container final com mais de um módulo MPM habilitado ao mesmo tempo
# (mpm_event + mpm_prefork), o que impede o Apache de iniciar com o erro
# "AH00534: More than one MPM loaded". Isso se mostrou persistente mesmo
# após corrigir o Dockerfile e recriar o projeto do zero no Railway, então
# em vez de confiar que o build corrige isso de uma vez por todas, forçamos
# a correção aqui, toda vez que o CONTAINER inicia — imediatamente antes de
# subir o Apache de verdade. a2dismod/a2enmod são idempotentes (não falham
# se o estado já estiver correto), então isso é seguro de rodar sempre.
echo "=== [ENTRYPOINT] Corrigindo módulos MPM antes de iniciar o Apache ==="
a2dismod -f mpm_event mpm_worker >/dev/null 2>&1 || true
a2enmod mpm_prefork >/dev/null 2>&1
a2enmod rewrite >/dev/null 2>&1

echo "=== [ENTRYPOINT] mods-enabled (mpm) após a correção ==="
ls -la /etc/apache2/mods-enabled/ | grep -i mpm

echo "=== [ENTRYPOINT] Iniciando apache2-foreground ==="
exec apache2-foreground
