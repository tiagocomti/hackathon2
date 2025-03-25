#!/bin/sh

# Verifica se o banco de dados está disponível

host="$1"
shift
cmd="$@"

until mysql -h "$host" -u "$MYSQL_USER" -p"$MYSQL_PASSWORD" -e 'exit'; do
  >&2 echo "Banco de dados ainda não está disponível - aguardando..."
  sleep 1
done

>&2 echo "Banco de dados está disponível - executando o comando"

exec $cmd