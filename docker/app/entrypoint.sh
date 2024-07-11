#!/bin/bash

# Start Supervisor in the background
supervisord &

# Start PHP-FPM
php-fpm
