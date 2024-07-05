# 1. Marinabot

WhatsApp Chatbot for E-commerce

This is a WhatsApp chatbot project that helps customers with their purchases on an e-commerce store. The chatbot uses a workflow gerated by a flow chart creator per connection. It integrates with the e-commerce store's API to provide a seamless customer experience.

![[marinabot]()](https://i.imgur.com/FgPbPGA.png)

## 1.1. Features

Responds to customer inquiries about products
Recommends products based on customer preferences
Provides product information and pricing
Allows customers to make purchases directly from the chatbot
Integrates with e-commerce store's API for seamless checkout

* PHP 8.3
* Laravel 11
* laravel reverb
* Laravel Sail
* Laravel Pest
* Laravel Pint
* Laravel Sanctum
* laravel tinker
* Laravel Pail
* Laravel Promps
* Mysql
* Redis
* Docker
* Docker Composer
* E-commerce store API (braip)
* Whataspp Evolution API

## 1.2. Design Patterns

    Factory pattern to integration and payment checkout
    Repository pattern to isolate code steps and repetitive code, 
    Strategy pattern to notification features.

## 1.3. How it Works

    Customer initiates conversation with the chatbot on WhatsApp
    chatbot responds to customer inquiries using EVOLUTION API
    chatbot recommends products based on customer preferences by connection
    Customer selects product and marinabot flow that`s provides a product url link.
    soon Customer makes purchase directly from the chatbot
    chatbot integrates with e-commerce store's API for seamless checkout

## 1.4. Setup

Welcome to marinaBot, create a new branch and starting coding with us.
Step to setup project in your machine.

### 1.4.1. Install docker and docker compose

Docker and Docker Compose installation method varies depending on your system.
For Linux Ubuntu:

Install Docker using the convenience script provided by Docker:

``` bash

curl -fsSL https://get.docker.com -o get-docker.sh && sh get-docker.sh

```

After installation, give your user the permission to run Docker commands:

```bash

sudo usermod -aG docker ${USER}

```

To apply these new permissions, log out and log back in, or you can use:

```bash

newgrp docker

```

Download the current stable release of Docker Compose with:

```bash

sudo curl -L "https://github.com/docker/compose/releases/download/1.29.2/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose

```

Apply executable permissions to the binary:

```bash

sudo chmod +x /usr/local/bin/docker-compose

```

Verify the installation by checking the version:

```bash

docker-compose --version

```

Now install Laravel sail following this steps: <https://laravel.com/docs/11.x/sail#installing-sail-into-existing-applications>

then start development server with:

```bash

sail up -d 

 # or 

 ./vendor/bin/sail up -d

```

### 1.4.2. project setup

Make sure to install the dependencies and run migrations:

```bash

# Laravel sail

sail artisan install && sail artisan migrate
```

## 1.5. Development Server

Start the development server on `http://laravel.test or localhost`:

```bash
sail up -d
```

When you done, close server connection with:

```bash
sail down -d
```

Check out the [Laravel Documentation](https://laravel.com/docs) and [Postman Collection](https://www.postman.com/wladiveras/workspace/nuvem/collection/10368732-2cd24e99-b9a2-498b-8252-9efa614019ce?action=share&creator=10368732&active-environment=10368732-60c8c68b-9bda-4183-9f20-f573bca4936d) for more information.

## 1.6. Utils Links

[Evolution API](https://doc.evolution-api.com/pt/get-started/introduction)
[Laravel Pint](https://laravel.com/docs/11.x/pint)
[Laravel Eloquent](https://laravel.com/docs/11.x/eloquent)
[Laravel Rerverb](https://reverb.laravel.com/)
[Introduction to the Repository Pattern](https://tallstackdev.medium.com/introduction-to-the-repository-pattern-in-laravel-c025eb1cc7fd)
[Understanding the Strategy Design Pattern in PHP](https://hashemirafsan.medium.com/understanding-the-strategy-design-pattern-in-php-with-a-simple-example-775791d30be1)
[Understanding the Factory Design Pattern in PHP](https://hashemirafsan.medium.com/understanding-the-factory-design-pattern-in-php-ddae58b59f25)

## 1.7. Licence

[MIT](./LICENSE)
