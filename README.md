# Daily Trends API

## Docker
Se utiliza Docker como entorno de **desarrollo local**.

Para comenzar crea la red de Avantio (_recomendado_). Permitirá comunicar diferentes proyectos en un futuro.
```
docker network create avantio
```

Antes de construir tus contenedores, asegúrate de tener las variables de entorno `UNAME` y `UID` en tu archivo `.env`.

> En Linux o Mac puedes abrir el terminal y escribir `id` para conocer dichas variables.

A continuación, puedes hacer build:
```
docker compose build
```

No olvides agregar `-d` para que se ejecute en segundo plano:
```
docker compose up -d
```

Con el fin de facilitar el uso de la **CLI** de **Symfony** y evitar conflictos de permisos, cuando accedas al container inicia sesión con `su` seguido del nombre de usuario que hayas especificado en el `UNAME`.

Por ejemplo:
```
docker exec -it server_daily_trends_api bash
```

Y a continuación:
```
su mati
```
Esto nos permitirá ejecutar comandos propios de la CLI de Symfony, como `maker` sin entrar en conflictos entre _root_ y nuestro _usuario local_.

### Symfony
Se ha configurado **NGINX** para trabajar con el dominio local http://avantio.test:8027/.
> Asegúrate de añadir `127.0.0.1	avantio.test` en tu archivo `/etc/hosts` local.

### Mongo Express
Simplemente cambia el puerto (8081) y accede a http://avantio.test:8081/.
