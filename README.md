# REST-Server for Personal Notes Management Tool

This is the REST-API Server for the Personal Notes Management Tool.

## Install

    git clone https://github.com/tbreuss/notes-server.git
    cd notes-server
    composer install

## Create/import database

To be done.

## Config

Copy configuration files:

    dev.dist.php -> dev.env.php
    prod.dist.php -> prod.env.php
    test.dist.php -> test.env.php

Edit configuration settings.        

## Run

    cd notes-server
    composer dev
    
Open your webbrowser <http://localhost:9999/api.php/ping>

You should see:

    {"name":"ch.tebe.notes","time":"2018-01-29T22:17:37+01:00","version":"0.5"}  

## Demo

https://notes.tebe.ch  
Username: github  
Password: github
