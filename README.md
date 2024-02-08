# Project Name
Api Ecommerce
## Description

This project is a api ecommerce using Laravel 10 

## Setup Instructions

### Prerequisites

- PHP - 8.1
- Composer

### Installation

1. Clone the repository
2. Install dependencies
```shc
composer install
```
3. Setup environment variables in `.env` file
```shc
cp .env.example .env
```
4. Generate application key
```shc
php artisan key:generate
```
5. Run migrations
```shc
php artisan migrate
```
6. Crawling data
```shc
php artisan crawl:geo-data
```
7. Run the server
```shc
php artisan serve
```

