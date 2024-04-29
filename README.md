<!-- # Employee Management System Backend

#### This is the backend implementation for the Employee Management System project.

## Installation

### Clone the Repository and Install Dependencies
`
git clone https://github.com/rohitatzignuts/Employee-Management-System-Backend
`
`
composer install
`

### Create Database
#### Create a new database named laravel using MySQL.

### Sign up on [Mail Trap](https://mailtrap.io/ "Mail Trap") and Add Mail Config in the .env file along with other Databse Configarations


```
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-mailtrap-username
MAIL_PASSWORD=your-mailtrap-password
MAIL_ENCRYPTION=tls
```

#### Create a new database named laravel using MySQL.

### Run Migrations and Seeders
`
php artisan migrate --seed
`

### Serve the Project
`
php artisan serve
`
 -->

---

# Employee Management System

This is the backend implementation for the Employee Management System project.

## Table of Contents

1. [Installation](#installation)
2. [Configuration](#configuration)
3. [Usage](#usage)
4. [Contributing](#contributing)

## Installation

1. **Clone the repository**

    ```bash
    git clone https://github.com/rohitatzignuts/Employee-Management-System-Backend
    ```

2. **Navigate into the project directory**

    ```bash
    cd Employee-Management-System-Backend
    ```

3. **Install dependencies**

    ```bash
    composer install
    ```

4. **Run Migrations and Seeders**

    ```bash
    php artisan migrate --seed
    ```

## Configuration

1. **Database Setup**

    - Create a new database for the project.
    - Update the `.env` file with your database credentials.

2. **Sign up on [Mail Trap](https://mailtrap.io/ "Mail Trap") and Add Mail Config in the .env file along with other Databse Configarations**

    ```bash
    MAIL_MAILER=smtp
    MAIL_HOST=smtp.mailtrap.io
    MAIL_PORT=2525
    MAIL_USERNAME=your-mailtrap-username
    MAIL_PASSWORD=your-mailtrap-password
    MAIL_ENCRYPTION=tls
    ```

## Usage

1. **Serve the application**

    ```bash
    php artisan serve
    ```

2. **Access the application**

    Open your web browser and navigate to `http://127.0.0.1:8000`.

## Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create a new branch (`git checkout -b feature`)
3. Make your changes
4. Commit your changes (`git commit -am 'Add new feature'`)
5. Push to the branch (`git push origin feature`)
6. Create a new Pull Request


