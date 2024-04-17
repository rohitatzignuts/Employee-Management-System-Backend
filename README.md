# Employee Management System Backend

#### This is the backend implementation for the Employee Management System project.

## Installation

### Clone the Repository and Install Dependencies
`git clone https://github.com/rohitatzignuts/Employee-Management-System-Backend`
`composer install`

### Create Database
#### Create a new database named laravel using MySQL.

### Sign up on [Mail Trap](https://mailtrap.io/ "Mail Trap") and Add Mail Config in the .env file along with other Databse Configarations 


``MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-mailtrap-username
MAIL_PASSWORD=your-mailtrap-password
MAIL_ENCRYPTION=tls``

#### Create a new database named laravel using MySQL.

### Run Migrations and Seeders
`php artisan migrate --seed`

### Serve the Project
`php artisan serve`

