# Notes Management Tool – REST-Server

The REST-API Server for the Notes Management Tool.

Note: This is the server only.
You need the appropriate client which is hosted at <https://github.com/tbreuss/notes-client>.

## Install

    git clone https://github.com/tbreuss/notes-server.git
    cd notes-server
    composer install

## Create/import database

Create a database at your hosting provider and import `config\mysql-dump.sql`.

## Config

Copy configuration files:

    cd config
    cp dev.dist.php dev.env.php
    cp prod.dist.php prod.env.php
    cp test.dist.php test.env.php

Open at least the `prod.env.php` config file and edit the settings.

## Run

    cd notes-server
    composer dev

To prevent timeout issues you can use the `COMPOSER_PROCESS_TIMEOUT` environment variable like this.

    COMPOSER_PROCESS_TIMEOUT=0 composer dev

Open your webbrowser <http://localhost:9999/api.php/ping>

You should see:

    {"name":"ch.tebe.notes","time":"2018-01-29T22:17:37+01:00","version":"0.5"}  

