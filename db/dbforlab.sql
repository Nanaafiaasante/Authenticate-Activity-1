-- ============================================================
-- Complete Database Setup for VendorConnect Ghana
-- Run this file in phpMyAdmin after dropping the database
-- ============================================================

-- Drop and recreate database
DROP DATABASE IF EXISTS `shoppn`;
CREATE DATABASE IF NOT EXISTS `shoppn` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `shoppn`;

-- ============================================================
-- CORE TABLES
-- ============================================================

-- Customer/User table (must be created first due to foreign keys)
CREATE TABLE `customer` (
  `customer_id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_name` varchar(100) NOT NULL,
  `customer_email` varchar(50) NOT NULL,
  `customer_pass` varchar(150) NOT NULL,
  `customer_country` varchar(30) NOT NULL,
  `customer_city` varchar(30) NOT NULL,
  `customer_contact` varchar(20) NOT NULL,
  `customer_image` varchar(100) DEFAULT NULL,
  `user_role` int(11) NOT NULL COMMENT '0=customer, 1=planner/vendor',
  `vendor_name` varchar(100) DEFAULT NULL COMMENT 'Business name for vendors',
  `about` text DEFAULT NULL COMMENT 'Vendor description',
  `profile_picture` varchar(255) DEFAULT NULL COMMENT 'Vendor profile picture path',
  `subscription_tier` varchar(20) DEFAULT NULL COMMENT 'starter or premium for planners',
  `subscription_status` varchar(20) DEFAULT 'active' COMMENT 'active, expired, cancelled',
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city_location` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`customer_id`),
  UNIQUE KEY `customer_email` (`customer_email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Brands table
CREATE TABLE `brands` (
  `brand_id` int(11) NOT NULL AUTO_INCREMENT,
  `brand_name` varchar(100) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`brand_id`),
  UNIQUE KEY `unique_brand_user` (`brand_name`, `user_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `brands_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `customer` (`customer_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Categories table
CREATE TABLE `categories` (
  `cat_id` int(11) NOT NULL AUTO_INCREMENT,
  `cat_name` varchar(100) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`cat_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `customer` (`customer_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Products table
CREATE TABLE `products` (
  `product_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_cat` int(11) NOT NULL,
  `product_brand` int(11) NOT NULL,
  `product_title` varchar(200) NOT NULL,
  `product_price` double NOT NULL,
  `product_desc` varchar(500) DEFAULT NULL,
  `product_image` varchar(100) DEFAULT NULL,
  `product_keywords` varchar(100) DEFAULT NULL,
  `user_id` int(11) NOT NULL COMMENT 'Owner/Planner ID - who created this product',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`product_id`),
  KEY `product_cat` (`product_cat`),
  KEY `product_brand` (`product_brand`),
  KEY `user_id` (`user_id`),
  KEY `idx_user_products` (`user_id`, `product_id`),
  CONSTRAINT `products_ibfk_1` FOREIGN KEY (`product_cat`) REFERENCES `categories` (`cat_id`) ON DELETE RESTRICT,
  CONSTRAINT `products_ibfk_2` FOREIGN KEY (`product_brand`) REFERENCES `brands` (`brand_id`) ON DELETE RESTRICT,
  CONSTRAINT `products_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `customer` (`customer_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Product package items table (what's included in each package)
CREATE TABLE `product_package_items` (
  `item_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `item_name` varchar(200) NOT NULL COMMENT 'e.g., Food, Transportation, Venue, Decorations',
  `is_optional` tinyint(1) DEFAULT 1 COMMENT '1=customer can deselect, 0=mandatory',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`item_id`),
  KEY `product_id` (`product_id`),
  KEY `idx_product_items` (`product_id`, `item_id`),
  CONSTRAINT `product_package_items_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Cart table
CREATE TABLE `cart` (
  `p_id` int(11) NOT NULL,
  `ip_add` varchar(50) NOT NULL,
  `c_id` int(11) DEFAULT NULL,
  `qty` int(11) NOT NULL,
  `selected_items` text DEFAULT NULL COMMENT 'JSON array of selected package item IDs',
  KEY `p_id` (`p_id`),
  KEY `c_id` (`c_id`),
  CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`p_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`c_id`) REFERENCES `customer` (`customer_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Wishlist table
CREATE TABLE `wishlist` (
  `wishlist_id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `added_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`wishlist_id`),
  UNIQUE KEY `unique_customer_product` (`customer_id`, `product_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `wishlist_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `wishlist_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Orders table
CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `invoice_no` int(11) NOT NULL,
  `order_date` date NOT NULL,
  `order_status` varchar(100) NOT NULL,
  `rating` decimal(2,1) DEFAULT NULL COMMENT 'Customer rating 1-5',
  `review_comment` text DEFAULT NULL COMMENT 'Customer review text',
  `vendor_id` int(11) DEFAULT NULL COMMENT 'Vendor who fulfilled the order',
  PRIMARY KEY (`order_id`),
  KEY `customer_id` (`customer_id`),
  KEY `vendor_id` (`vendor_id`),
  KEY `idx_rating` (`vendor_id`, `rating`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`),
  CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`vendor_id`) REFERENCES `customer` (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Order details table
CREATE TABLE `orderdetails` (
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `qty` int(11) NOT NULL,
  `selected_items` text DEFAULT NULL COMMENT 'JSON array of selected package items with their names',
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `orderdetails_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  CONSTRAINT `orderdetails_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Stores individual order line items with selected package items';

-- Payment table
CREATE TABLE `payment` (
  `pay_id` int(11) NOT NULL AUTO_INCREMENT,
  `amt` double NOT NULL,
  `customer_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `currency` text NOT NULL,
  `payment_date` date NOT NULL,
  `payment_method` varchar(50) DEFAULT 'direct' COMMENT 'Payment method: paystack, cash, bank_transfer, mobile_money',
  `transaction_ref` varchar(100) DEFAULT NULL COMMENT 'Paystack transaction reference',
  `authorization_code` varchar(100) DEFAULT NULL COMMENT 'Authorization code from payment gateway',
  `payment_channel` varchar(50) DEFAULT NULL COMMENT 'Payment channel: card, mobile_money, bank, ussd',
  PRIMARY KEY (`pay_id`),
  KEY `customer_id` (`customer_id`),
  KEY `order_id` (`order_id`),
  KEY `idx_transaction_ref` (`transaction_ref`),
  KEY `idx_payment_method` (`payment_method`),
  KEY `idx_payment_channel` (`payment_channel`),
  CONSTRAINT `payment_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`),
  CONSTRAINT `payment_ibfk_2` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- CONSULTATION SYSTEM TABLES
-- ============================================================

-- Service types for consultations
CREATE TABLE `service_types` (
  `service_id` int(11) NOT NULL AUTO_INCREMENT,
  `service_name` varchar(100) NOT NULL,
  `service_description` text,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`service_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Planner availability time slots
CREATE TABLE `consultation_slots` (
  `slot_id` int(11) NOT NULL AUTO_INCREMENT,
  `planner_id` int(11) NOT NULL,
  `day_of_week` int(1) NOT NULL COMMENT '0=Sunday, 1=Monday, ..., 6=Saturday',
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `is_available` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`slot_id`),
  KEY `planner_id` (`planner_id`),
  KEY `idx_slot_planner_day` (`planner_id`, `day_of_week`, `is_available`),
  CONSTRAINT `consultation_slots_ibfk_1` FOREIGN KEY (`planner_id`) REFERENCES `customer` (`customer_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Consultation bookings
CREATE TABLE `consultations` (
  `consultation_id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `planner_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL COMMENT 'Links consultation to product order',
  `service_id` int(11) DEFAULT NULL,
  `consultation_date` date NOT NULL,
  `consultation_time` time NOT NULL,
  `duration_minutes` int(11) DEFAULT 60,
  `consultation_fee` decimal(10,2) NOT NULL,
  `booking_status` enum('pending','confirmed','completed','cancelled','no-show') DEFAULT 'pending',
  `payment_status` enum('unpaid','paid','refunded') DEFAULT 'unpaid',
  `payment_reference` varchar(100) DEFAULT NULL,
  `customer_notes` text,
  `planner_notes` text,
  `meeting_location` varchar(255) DEFAULT NULL,
  `meeting_link` varchar(255) DEFAULT NULL COMMENT 'For virtual consultations',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`consultation_id`),
  KEY `customer_id` (`customer_id`),
  KEY `planner_id` (`planner_id`),
  KEY `service_id` (`service_id`),
  KEY `order_id` (`order_id`),
  KEY `consultation_date` (`consultation_date`),
  KEY `booking_status` (`booking_status`),
  KEY `idx_consultation_datetime` (`consultation_date`, `consultation_time`),
  KEY `idx_payment_reference` (`payment_reference`),
  CONSTRAINT `consultations_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON DELETE CASCADE,
  CONSTRAINT `consultations_ibfk_2` FOREIGN KEY (`planner_id`) REFERENCES `customer` (`customer_id`) ON DELETE CASCADE,
  CONSTRAINT `consultations_ibfk_3` FOREIGN KEY (`service_id`) REFERENCES `service_types` (`service_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- DEFAULT DATA
-- ============================================================

-- Insert default service types for consultations
INSERT INTO `service_types` (`service_name`, `service_description`) VALUES
('Initial Consultation', 'First-time consultation to discuss event requirements and planning'),
('Venue Selection', 'Assistance with selecting and booking the perfect venue'),
('Budget Planning', 'Detailed budget breakdown and cost management advice'),
('Design & Decoration', 'Event styling, theme development, and decoration planning'),
('Vendor Coordination', 'Help with selecting and managing event vendors'),
('Day-of Coordination', 'On-site event management and coordination'),
('Full Event Planning', 'Comprehensive planning from start to finish'),
('Follow-up Consultation', 'Post-event review or additional planning support');

-- ============================================================
-- NOTES FOR DEVELOPERS
-- ============================================================
-- This schema includes:
-- 1. All base tables for e-commerce (products, cart, orders, payment)
-- 2. User management with role support (customer vs planner)
-- 3. Location fields for proximity matching
-- 4. Consultation booking system
-- 5. Planner availability management
-- 6. Proper foreign key constraints and indexes
-- 7. Default service types for consultations
-- 
-- After importing:
-- 1. Create your first user account (will be customer_id = 1)
-- 2. Planners should set user_role = 1
-- 3. Customers should set user_role = 0
-- 4. Planners can add their brands, categories, and products
-- 5. Planners should set their availability in consultation_slots
-- ============================================================

COMMIT;




ALTER TABLE `customer` 
ADD COLUMN `reset_token` VARCHAR(64) NULL DEFAULT NULL AFTER `subscription_tier`,
ADD COLUMN `reset_token_expiry` DATETIME NULL DEFAULT NULL AFTER `reset_token`,
ADD INDEX `idx_reset_token` (`reset_token`);


-- Add location coordinates to products table ONLY
-- (customer table already has latitude/longitude in dbforlab.sql)
ALTER TABLE `products` 
ADD COLUMN `latitude` DECIMAL(10, 8) NULL DEFAULT NULL COMMENT 'Product/Vendor location latitude',
ADD COLUMN `longitude` DECIMAL(11, 8) NULL DEFAULT NULL COMMENT 'Product/Vendor location longitude',
ADD INDEX `idx_product_coordinates` (`latitude`, `longitude`);

-- Add location_updated_at to customer table to track when location was last updated
ALTER TABLE `customer`
ADD COLUMN `location_updated_at` DATETIME NULL DEFAULT NULL COMMENT 'Last time location was updated';

-- Add index for customer coordinates (already exist, just adding index)
ALTER TABLE `customer`
ADD INDEX `idx_customer_coordinates` (`latitude`, `longitude`);

-- Note: Run this SQL in your phpMyAdmin or MySQL client to add location tracking
-- The customer table already has latitude/longitude columns from dbforlab.sql


-- ============================================================
-- DATA CLEANUP & INTEGRITY FIXES
-- ============================================================

-- Clean up any NULL or 'undefined' values in cart selected_items
UPDATE `cart` 
SET `selected_items` = NULL 
WHERE `selected_items` = '' 
   OR `selected_items` = 'null' 
   OR `selected_items` = 'undefined';

-- Remove item_description column from product_package_items if it exists
-- (This column was removed from the schema, cleanup for existing databases)
-- Drop the old column if it exists. Older MySQL versions do not support
-- `DROP COLUMN IF EXISTS`, so use an INFORMATION_SCHEMA check and a
-- prepared statement which is compatible across versions.
-- NOTE: Run this block in phpMyAdmin or your MySQL client. It does not
-- require creating a stored procedure.
-- Check for the column and run an ALTER only when present.
SELECT COUNT(*) INTO @col_count
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = 'shoppn'
  AND TABLE_NAME = 'product_package_items'
  AND COLUMN_NAME = 'item_description';

SET @s = IF(@col_count > 0,
    'ALTER TABLE `product_package_items` DROP COLUMN `item_description`;',
    'SELECT 1;'
);
PREPARE stmt FROM @s;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
