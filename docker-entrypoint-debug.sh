#!/bin/bash
# Script de diagnóstico temporário: imprime, em tempo de execução (não em
# build), o estado real dos módulos MPM do Apache dentro do container que
# está de fato rodando — para eliminar qualquer dúvida sobre cache de build,
# volumes sobrepostos, ou comando de start customizado sobrescrevendo a imagem.
echo "=== [RUNTIME] mods-enabled (mpm) ==="
ls -la /etc/apache2/mods-enabled/ | grep -i mpm

echo "=== [RUNTIME] LoadModule mpm em apache2.conf, ports.conf, mods/conf-enabled ==="
grep -Rn "LoadModule mpm" /etc/apache2/apache2.conf /etc/apache2/ports.conf /etc/apache2/mods-enabled/ /etc/apache2/conf-enabled/ 2>/dev/null

echo "=== [RUNTIME] MPM compilado por padrão no binário do Apache ==="
apache2ctl -V 2>&1 | grep -i mpm

echo "=== [RUNTIME] Variaveis de ambiente relevantes ==="
env | grep -i "APACHE\|PORT" || true

echo "=== Iniciando apache2-foreground ==="
exec apache2-foreground
