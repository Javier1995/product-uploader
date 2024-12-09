# Product Uploader

This project provides a command-line tool to upload products using csv file.



## Installation

To install the Product Uploader, follow these steps:

1. Install your local MySQL server first.

2. Clone the repository:
    ```sh
    git clone https://github.com/Javier1995/product-uploader.git
    ```

3. Navigate to the project directory:
    ```sh
    cd product-uploader
    ```

4. Install the dependencies using Composer:
    ```sh
    composer install
    ```

5. Set up your environment variables:
    ```sh
    cp .env .env.local
    ```

5. Run the database migrations:
    ```sh
    php bin/console doctrine:migrations:migrate
    ```

### Usage

```sh
   php bin/console app:upload-product [options]
```

### Options

- ` <path>`: Specify the path to file (required).
- `--test`: Run in test mode (does not insert data into the database)

### Example

`
php bin/console app:upload-product  stock.csv
`

![Captura de pantalla 2024-12-09 024307](https://github.com/user-attachments/assets/3e6e1082-92e5-495e-8b49-cc47cb6575e8)



`
php bin/console app:upload-product  stock.csv --test
`

![Captura de pantalla 2024-12-09 024405](https://github.com/user-attachments/assets/bae773b8-1b71-49a7-aea5-d6a705dab3f7)


