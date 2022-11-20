# tic-tac-toe

## Project Setup
```
cp .env .env.local
```

set `DATABASE_URL` environment variable in `.env.local` (default values are ok)

```
composer install
```

start database container if chosen database is postgresql with:
```
docker-compose up -d
```

create database if it doesn't already exists with:
```
php bin/console d:d:c
```

update database schema with:
```
php bin/console d:s:u --force
```

install Symfony CLI as described here: https://symfony.com/download

start local web server with:
```
symfony server:start --no-tls
```

app should be available at:
```
http://127.0.0.1:8000 
```

to run the test suite:
```
# Create dir
mkdir -p var/data

# Create test sqlite database
php bin/console --env test d:d:c

# Setup database
php bin/console --env test d:s:u --force

# Run tests
./bin/phpunit
```