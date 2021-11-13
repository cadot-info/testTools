# run docker compose and change DIR in docker-compose by curre,t directory

result=${PWD##*/}
docker-compose down --remove-orphans
sed -i 's?{DIR}?'$result'?' docker-compose.yml
docker-compose up -d
docker inspect --format='{{range $p, $conf := .NetworkSettings.Ports}}  {{(index $conf 0).HostPort}} {{end}}' $result
