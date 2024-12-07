# Overstock Daily Deals

Overstock Daily Deals is a web application that allows users to browse and purchase products at discounted prices. The application includes features for managing products, orders, and customers, as well as user authentication and authorization.

## Table of Contents

- [Features](#features)
- [Installation](#installation)
- [Usage](#usage)
- [File Structure](#file-structure)
- [Contributing](#contributing)
- [License](#license)

## Features

- User registration and login
- Browse products with discounts
- View product details and additional images
- Add products to orders
- Manage products, orders, and customers (admin only)
- Manage deals and discounts (admin only)
- Audit logging for changes (admin only)

## Installation

1. Clone the repository:
    ```sh
    git clone https://github.com/kevint024/overstock.git
    ```
2. Navigate to the project directory:
    ```sh
    cd overstock-daily-deals
    ```
3. Set up the database:
    - Create a MySQL database named `overstock`.
    - Import the database schema from `overstock.sql` (if available).
    - Update the database connection details in [`admin/db_connection.php`](admin/db_connection.php).

4. Start a local server using XAMPP or WAMP and place the project directory in the server's root directory.

## Usage

1. Open your web browser and navigate to `http://localhost/overstock-daily-deals`.
2. Register a new user account or log in with an existing account.
3. Browse products, view details, and add products to your order.
4. Admin users can manage products, orders, and customers through the admin interface.

## Important Notes

- XAMPP with MySQL and Apache is recommended for deployment.
- Regular accounts can be created through the registration interface.
- Admin accounts must be directly inserted into the database through phpMyAdmin.
- Select Images for certain products are hosted in this repo. It is recommended that you replace these images upon deployment.


## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details. Generative AI has also been used in the development of this project to assist in debugging, writing, and editing modules.
