CREATE DATABASE php_transactions;

use php_transactions;

CREATE TABLE transactions(
	id INT AUTO_INCREMENT PRIMARY KEY,
    transaction_date DATE NOT NULL,
    check_number VARCHAR(50) NULL,
    description VARCHAR(255) NOT NULL,
    amount DECIMAL(10,2) NOT NULL
);