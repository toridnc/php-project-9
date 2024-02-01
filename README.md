### Hexlet tests and linter status:
[![Actions Status](https://github.com/toridnc/php-project-9/workflows/hexlet-check/badge.svg)](https://github.com/toridnc/php-project-9/actions) [![PHP_CodeSniffer](https://github.com/toridnc/php-project-9/actions/workflows/project-check.yml/badge.svg)](https://github.com/toridnc/php-project-9/actions) [![Maintainability](https://api.codeclimate.com/v1/badges/87ddaf487bc8b15b6f48/maintainability)](https://codeclimate.com/github/toridnc/php-project-9/maintainability)

<br>

# Анализатор страниц

Сайт, который анализирует указанные страницы на SEO пригодность по аналогии с [PageSpeed Insights](https://pagespeed.web.dev/). Анализатор страниц – полноценное приложение на базе фреймворка Slim. Здесь отрабатываются базовые принципы построения современных сайтов на MVC-архитектуре: работа с роутингом, обработчиками запросов и шаблонизатором, взаимодействие с базой данных.

# Page analyzer

This is a site that analyzes the specified pages for SEO suitability, similar to [PageSpeed Insights](https://pagespeed.web.dev/). The page Analyzer is a full-fledged application based on the Slim framework. Here, the basic principles of building modern websites on the MVC architecture are worked out: working with routing, query handlers and a template engine, interacting with a database.

<br>

## System requirements
* PHP 7.4
* Slim ^4.10
* PostgreSQL ^12 (15.1)
* Composer

<br>

## Install
```sh
git clone git@github.com:toridnc/php-project-9.git
```
```sh
cd php-project-9
```
```sh
make install
```

## Start
```sh
make start
```
