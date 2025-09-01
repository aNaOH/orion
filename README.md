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

The admin credentials:

Email: admin@togetheronorion.com
Password: 0R1ON_together