## Installation

Please check the official laravel installation guide for server requirements before you start. [Official Documentation](https://laravel.com/docs/11.x)

Clone the repository (use the token provided in the email as password)

    git clone https://github.com/viilance/intus-dev-task.git

Switch to the repo folder

    cd intus-dev-task

Install all the dependencies using composer

    composer install

Copy the example env file and make the required configuration changes in the .env file

    cp .env.example .env

Generate the application key

    php artisan key:generate

Since we are using the default sqlite as db, make sure that you have the required driver

    sudo apt-get install php-sqlite3

Run the database migrations (**Set the database connection in .env before migrating**)

    php artisan migrate

Start Laravel's local development server using Laravel Artisan's serve command

    php artisan serve

compile your application's frontend assets (**Make sure that you have updated node**)

    npm install
    npm run dev

You can now access the application at http://127.0.0.1:8000/

**TL;DR command list**

    git clone https://github.com/viilance/intus-dev-task.git
    cd intus-dev-task
    composer install
    cp .env.example .env
    php artisan key:generate
    sudo apt-get install php-sqlite3
    php artisan migrate
    php artisan serve
    npm install
    npm run dev

----------
