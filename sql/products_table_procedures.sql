-- =============================================
-- Products table + CRUD stored procedures
-- Database: workshop (change if needed)
-- =============================================

CREATE DATABASE IF NOT EXISTS workshop;
USE workshop;

DROP TABLE IF EXISTS products;
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    category_id INT NULL,
    brand VARCHAR(100) NULL,
    price DECIMAL(10,2) NOT NULL,
    discount TINYINT UNSIGNED NOT NULL DEFAULT 0,
    final_price DECIMAL(10,2) GENERATED ALWAYS AS (
        ROUND(price - ((price * IFNULL(discount, 0)) / 100), 2)
    ) STORED,
    stock INT NULL DEFAULT 0,
    description TEXT NULL,
    long_description LONGTEXT NULL,
    image VARCHAR(255) NULL,
    gallery_images JSON NULL,
    status ENUM('Active', 'Inactive') NOT NULL DEFAULT 'Active'
);

DROP PROCEDURE IF EXISTS sp_products_insert;
DROP PROCEDURE IF EXISTS sp_products_read;
DROP PROCEDURE IF EXISTS sp_products_edit;
DROP PROCEDURE IF EXISTS sp_products_update;
DROP PROCEDURE IF EXISTS sp_products_delete;

DELIMITER $$

CREATE PROCEDURE sp_products_insert(
    IN p_name VARCHAR(255),
    IN p_category_id INT,
    IN p_brand VARCHAR(100),
    IN p_price DECIMAL(10,2),
    IN p_discount INT,
    IN p_stock INT,
    IN p_description TEXT,
    IN p_long_description LONGTEXT,
    IN p_image VARCHAR(255),
    IN p_gallery_images JSON,
    IN p_status VARCHAR(10)
)
BEGIN
    INSERT INTO products (
        name, category_id, brand, price, discount, stock,
        description, long_description, image, gallery_images, status
    ) VALUES (
        p_name, p_category_id, p_brand, p_price,
        CASE
            WHEN p_discount IS NULL OR p_discount < 0 THEN 0
            WHEN p_discount > 30 THEN 30
            ELSE p_discount
        END,
        p_stock,
        p_description, p_long_description, p_image, p_gallery_images,
        CASE WHEN p_status IN ('Active', 'Inactive') THEN p_status ELSE 'Active' END
    );

    -- SELECT LAST_INSERT_ID() AS inserted_id;
END $$

CREATE PROCEDURE sp_products_read()
BEGIN
    SELECT
        id, name, category_id, brand, price, discount, final_price,
        stock, description, long_description, image, gallery_images, status
    FROM products
    ORDER BY id DESC;
END $$

CREATE PROCEDURE sp_products_edit(IN p_id INT)
BEGIN
    SELECT
        id, name, category_id, brand, price, discount, final_price,
        stock, description, long_description, image, gallery_images, status
    FROM products
    WHERE id = p_id
    LIMIT 1;
END $$

CREATE PROCEDURE sp_products_update(
    IN p_id INT,
    IN p_name VARCHAR(255),
    IN p_category_id INT,
    IN p_brand VARCHAR(100),
    IN p_price DECIMAL(10,2),
    IN p_discount INT,
    IN p_stock INT,
    IN p_description TEXT,
    IN p_long_description LONGTEXT,
    IN p_image VARCHAR(255),
    IN p_gallery_images JSON,
    IN p_status VARCHAR(10)
)
BEGIN
    UPDATE products
    SET
        name = p_name,
        category_id = p_category_id,
        brand = p_brand,
        price = p_price,
        discount = CASE
            WHEN p_discount IS NULL OR p_discount < 0 THEN 0
            WHEN p_discount > 30 THEN 30
            ELSE p_discount
        END,
        stock = p_stock,
        description = p_description,    
        long_description = p_long_description,
        image = p_image,
        gallery_images = p_gallery_images,
        status = CASE WHEN p_status IN ('Active', 'Inactive') THEN p_status ELSE 'Active' END
    WHERE id = p_id;

    SELECT ROW_COUNT() AS affected_rows;
END $$

CREATE PROCEDURE sp_products_delete(IN p_id INT)
BEGIN
    DELETE FROM products WHERE id = p_id;
    SELECT ROW_COUNT() AS affected_rows;
END $$

DELIMITER ;
