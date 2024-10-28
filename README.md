# Orion
## Gamers, united!

Orion is a social network (to write)

# Requirements

- An Apache and PHP 8 environment
- PDO enabled on PHP
- A MySQL database
- Composer installed ([Get it here](https://getcomposer.org/doc/00-intro.md))
- A Cloudflare account and a bucket

# How to install

1. **Clone this repository using the terminal**

```
git clone https://github.com/aNaOH/orion.git
```

2. **Install Composer packages**

Depending on the Composer installation method used, the command can be one of the following:

```
composer install
php composer.phar install
```

3. **Configure environment**

Copy '.env.template' and rename to '.env', then fill all the environment variables:
 - **DB_HOST**: IP or hostname where the database is hosted
 - **DB_NAME**: Database name
 - **DB_USER**: Database auth user
 - **DB_PASS**: Database auth password
 - **CLOUDFLARE_ACCOUNT_ID**: [Get it here](https://dash.cloudflare.com/a971beed6aeeec36fc3a1cdacf80516f/r2/overview)
 - **CLOUDFLARE_R2_BUCKET_NAME**: Name of the Cloudflare bucket
 - **CLOUDFLARE_R2_TOKEN**: [Get it here](https://dash.cloudflare.com/a971beed6aeeec36fc3a1cdacf80516f/r2/api-tokens)
 - **CLOUDFLARE_R2_TOKEN_SECRET**: [Get it here](https://dash.cloudflare.com/a971beed6aeeec36fc3a1cdacf80516f/r2/api-tokens)

4. **Prepare the DB**

On your MySQL database manager with your database opened, execute 'orion.sql' to add all the tables and indexes.

5. **Add some files to the bucket**

Orion uses 2 static files hosted on the bucket:
 
 - **user/profile_pic/default.png**: A default user profile picture, when no one is uploaded
 - **misc/404.png**: A 404 image used when a media file is not found

# Install using Docker

1. **Install Docker**

Install Docker (Engine or Desktop) on your system, you can know how [here for Engine](https://docs.docker.com/engine/install/) or [here for Desktop](https://docs.docker.com/desktop/).

2. **Follow the 'How to install steps'**

Go [here](#how-to-install) and follow these steps.

3. **Build the Docker Image**

Open your terminal and use the next command, if needed, run it with privileges (admin or sudo):

```
docker build -t composer-quick .
```

4. **Create the Container**

Open your terminal and use the next command, if needed, run it with privileges (admin or sudo):

```
sudo docker run -d -p 8080:80 -v <Your project location>:/var/www/html --name orion-server composer-quick
```