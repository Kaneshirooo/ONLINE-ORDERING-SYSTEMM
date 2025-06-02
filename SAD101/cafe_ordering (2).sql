-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 26, 2025 at 06:52 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cafe_ordering`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customer_loyalty`
--

CREATE TABLE `customer_loyalty` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `points` int(11) NOT NULL DEFAULT 0,
  `tier` varchar(20) NOT NULL DEFAULT 'bronze',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer_loyalty`
--

INSERT INTO `customer_loyalty` (`id`, `user_id`, `points`, `tier`, `created_at`, `updated_at`) VALUES
(1, 2, 496, 'silver', '2025-05-20 12:53:10', '2025-05-25 13:02:45');

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `stock_quantity` int(11) NOT NULL DEFAULT 0,
  `low_stock_threshold` int(11) NOT NULL DEFAULT 10,
  `last_restock_date` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`id`, `product_id`, `stock_quantity`, `low_stock_threshold`, `last_restock_date`, `created_at`, `updated_at`) VALUES
(1, 2, 110, 10, '2025-05-20 13:15:21', '2025-05-20 13:13:35', '2025-05-20 13:15:21'),
(2, 18, 109, 10, '2025-05-20 13:15:21', '2025-05-20 13:13:35', '2025-05-20 13:15:21'),
(3, 20, 110, 10, '2025-05-20 13:15:21', '2025-05-20 13:13:35', '2025-05-20 13:15:21'),
(4, 23, 110, 10, '2025-05-20 13:15:21', '2025-05-20 13:13:35', '2025-05-20 13:15:21'),
(5, 16, 110, 10, '2025-05-20 13:15:21', '2025-05-20 13:13:35', '2025-05-20 13:15:21'),
(6, 3, 110, 10, '2025-05-20 13:15:21', '2025-05-20 13:13:35', '2025-05-20 13:15:21'),
(7, 8, 110, 10, '2025-05-20 13:15:21', '2025-05-20 13:13:35', '2025-05-20 13:15:21'),
(8, 21, 110, 10, '2025-05-20 13:15:21', '2025-05-20 13:13:35', '2025-05-20 13:15:21'),
(9, 1, 110, 10, '2025-05-20 13:15:21', '2025-05-20 13:13:35', '2025-05-20 13:15:21'),
(10, 7, 110, 10, '2025-05-20 13:15:21', '2025-05-20 13:13:35', '2025-05-20 13:15:21'),
(11, 14, 110, 10, '2025-05-20 13:15:21', '2025-05-20 13:13:35', '2025-05-20 13:15:21'),
(12, 4, 110, 10, '2025-05-20 13:15:21', '2025-05-20 13:13:35', '2025-05-20 13:15:21'),
(13, 15, 110, 10, '2025-05-20 13:15:21', '2025-05-20 13:13:35', '2025-05-20 13:15:21'),
(14, 24, 110, 10, '2025-05-20 13:15:21', '2025-05-20 13:13:35', '2025-05-20 13:15:21'),
(15, 12, 110, 10, '2025-05-20 13:15:21', '2025-05-20 13:13:35', '2025-05-20 13:15:21'),
(16, 17, 110, 10, '2025-05-20 13:15:21', '2025-05-20 13:13:35', '2025-05-20 13:15:21'),
(17, 9, 110, 10, '2025-05-20 13:15:21', '2025-05-20 13:13:35', '2025-05-20 13:15:21'),
(18, 11, 110, 10, '2025-05-20 13:15:21', '2025-05-20 13:13:35', '2025-05-20 13:15:21'),
(19, 5, 110, 10, '2025-05-20 13:15:21', '2025-05-20 13:13:35', '2025-05-20 13:15:21'),
(20, 13, 110, 10, '2025-05-20 13:15:21', '2025-05-20 13:13:35', '2025-05-20 13:15:21'),
(21, 19, 1109, 10, '2025-05-20 13:15:21', '2025-05-20 13:13:35', '2025-05-23 06:07:22'),
(22, 22, 109, 10, '2025-05-20 13:15:21', '2025-05-20 13:13:35', '2025-05-23 06:59:40'),
(23, 6, 109, 10, '2025-05-20 13:15:21', '2025-05-20 13:13:35', '2025-05-23 00:20:50'),
(24, 10, 110, 10, '2025-05-20 13:15:21', '2025-05-20 13:13:35', '2025-05-20 13:15:21'),
(25, 26, 110, 10, '2025-05-20 13:15:21', '2025-05-20 13:13:35', '2025-05-20 13:15:21'),
(26, 32, 110, 10, '2025-05-20 13:15:21', '2025-05-20 13:13:35', '2025-05-20 13:15:21'),
(27, 33, 110, 10, '2025-05-20 13:15:21', '2025-05-20 13:13:35', '2025-05-20 13:15:21'),
(28, 28, 110, 10, '2025-05-20 13:15:21', '2025-05-20 13:13:35', '2025-05-20 13:15:21'),
(29, 31, 110, 10, '2025-05-20 13:15:21', '2025-05-20 13:13:35', '2025-05-20 13:15:21'),
(30, 35, 110, 10, '2025-05-20 13:15:21', '2025-05-20 13:13:35', '2025-05-20 13:15:21'),
(31, 29, 110, 10, '2025-05-20 13:15:21', '2025-05-20 13:13:35', '2025-05-20 13:15:21'),
(32, 25, 110, 10, '2025-05-20 13:15:21', '2025-05-20 13:13:35', '2025-05-20 13:15:21'),
(33, 27, 110, 10, '2025-05-20 13:15:21', '2025-05-20 13:13:35', '2025-05-20 13:15:21'),
(34, 34, 110, 10, '2025-05-20 13:15:21', '2025-05-20 13:13:35', '2025-05-20 13:15:21'),
(35, 30, 110, 10, '2025-05-20 13:15:21', '2025-05-20 13:13:35', '2025-05-20 13:15:21'),
(36, 36, 110, 10, '2025-05-20 13:15:21', '2025-05-20 13:13:35', '2025-05-20 13:15:21');

-- --------------------------------------------------------

--
-- Table structure for table `loyalty_history`
--

CREATE TABLE `loyalty_history` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `points` int(11) NOT NULL,
  `reason` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `loyalty_history`
--

INSERT INTO `loyalty_history` (`id`, `user_id`, `points`, `reason`, `created_at`) VALUES
(1, 2, 10, 'Order #0', '2025-05-20 13:12:13'),
(2, 2, 10, 'Order #0', '2025-05-20 13:14:23'),
(3, 2, 9, 'Order #0', '2025-05-23 00:20:50'),
(4, 2, 11, 'Order #0', '2025-05-23 06:07:22'),
(5, 2, 9, 'Order #0', '2025-05-23 06:59:40'),
(6, 2, 103, 'Order #4', '2025-05-25 12:48:58'),
(7, 2, 72, 'Order #5', '2025-05-25 13:01:30'),
(8, 2, 72, 'Order #6', '2025-05-25 13:02:45');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','processing','completed','cancelled') DEFAULT 'pending',
  `payment_method` varchar(50) DEFAULT 'Cash on Delivery',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `discount_amount` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total_amount`, `status`, `payment_method`, `created_at`, `discount_amount`) VALUES
(1, 2, 335.00, 'completed', 'Pay at Pickup', '2025-05-17 08:53:05', 0.00),
(2, 2, 75.00, 'pending', 'Online Payment', '2025-05-17 10:59:27', 0.00),
(3, 2, 110.00, 'completed', 'Cash on Delivery', '2025-05-20 12:54:17', 0.00),
(4, 2, 103.50, 'pending', 'Cash on Delivery', '2025-05-25 12:48:58', 11.50),
(5, 2, 72.00, 'pending', 'Online Payment', '2025-05-25 13:01:30', 8.00),
(6, 2, 72.00, 'pending', 'Cash on Delivery', '2025-05-25 13:02:45', 8.00);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 1, 18, 1, 115.00),
(2, 1, 20, 1, 120.00),
(3, 1, 3, 1, 100.00),
(4, 2, 28, 1, 75.00),
(5, 3, 10, 1, 110.00),
(11, 4, 18, 1, 115.00),
(12, 5, 2, 1, 80.00),
(13, 6, 2, 1, 80.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `category` enum('coffee','pastries') NOT NULL,
  `ingredients` text DEFAULT NULL,
  `image` varchar(255) DEFAULT 'https://placeholder.svg?height=300&width=300',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `category`, `ingredients`, `image`, `created_at`) VALUES
(1, 'Double Espresso', 'Rich and intense double shot of espresso.', 60.00, 'coffee', 'Freshly ground coffee beans, Water', 'https://placeholder.svg?height=300&width=300', '2025-05-17 08:49:54'),
(2, 'Americano', 'Espresso diluted with hot water.', 80.00, 'coffee', 'Espresso, Hot water', 'https://placeholder.svg?height=300&width=300', '2025-05-17 08:49:54'),
(3, 'Capuccino', 'Equal parts espresso, steamed milk, and milk foam.', 100.00, 'coffee', 'Espresso, Steamed milk, Milk foam', 'https://placeholder.svg?height=300&width=300', '2025-05-17 08:49:54'),
(4, 'Latte', 'Espresso with steamed milk and a light layer of foam.', 100.00, 'coffee', 'Espresso, Steamed milk, Light milk foam', 'https://placeholder.svg?height=300&width=300', '2025-05-17 08:49:54'),
(5, 'Spanish Latte', 'Espresso with condensed milk and steamed milk.', 105.00, 'coffee', 'Espresso, Condensed milk, Steamed milk', 'https://placeholder.svg?height=300&width=300', '2025-05-17 08:49:54'),
(6, 'Vanilla Latte', 'Latte with vanilla syrup.', 105.00, 'coffee', 'Espresso, Steamed milk, Vanilla syrup, Light milk foam', 'https://placeholder.svg?height=300&width=300', '2025-05-17 08:49:54'),
(7, 'Hazelnut Latte', 'Latte with hazelnut syrup.', 105.00, 'coffee', 'Espresso, Steamed milk, Hazelnut syrup, Light milk foam', 'https://placeholder.svg?height=300&width=300', '2025-05-17 08:49:54'),
(8, 'Caramel Latte', 'Latte with caramel syrup.', 105.00, 'coffee', 'Espresso, Steamed milk, Caramel syrup, Light milk foam', 'https://placeholder.svg?height=300&width=300', '2025-05-17 08:49:54'),
(9, 'Mocha', 'Espresso with chocolate and steamed milk.', 110.00, 'coffee', 'Espresso, Chocolate syrup, Steamed milk, Whipped cream', 'https://placeholder.svg?height=300&width=300', '2025-05-17 08:49:54'),
(10, 'White Mocha Latte', 'Espresso with white chocolate and steamed milk.', 110.00, 'coffee', 'Espresso, White chocolate, Steamed milk, Whipped cream', 'https://placeholder.svg?height=300&width=300', '2025-05-17 08:49:54'),
(11, 'Salted Caramel Macchiato', 'Espresso with caramel, salt, and steamed milk.', 115.00, 'coffee', 'Espresso, Caramel syrup, Sea salt, Steamed milk, Vanilla', 'https://placeholder.svg?height=300&width=300', '2025-05-17 08:49:54'),
(12, 'Matcha Espresso Latte', 'Matcha and espresso with steamed milk.', 115.00, 'coffee', 'Espresso, Matcha powder, Steamed milk', 'https://placeholder.svg?height=300&width=300', '2025-05-17 08:49:54'),
(13, 'Strawberry Choco', 'Chocolate with strawberry flavor.', 115.00, 'coffee', 'Chocolate, Strawberry syrup, Steamed milk, Whipped cream', 'https://placeholder.svg?height=300&width=300', '2025-05-17 08:49:54'),
(14, 'Hersheys Choco', 'Hot chocolate made with Hershey\'s chocolate.', 100.00, 'coffee', 'Hershey\'s chocolate, Steamed milk, Whipped cream', 'https://placeholder.svg?height=300&width=300', '2025-05-17 08:49:54'),
(15, 'London Fog Tea Latte', 'Earl Grey tea with vanilla and steamed milk.', 100.00, 'coffee', 'Earl Grey tea, Vanilla syrup, Steamed milk', 'https://placeholder.svg?height=300&width=300', '2025-05-17 08:49:54'),
(16, 'Butterfly Pea Tea Latte', 'Butterfly pea flower tea with steamed milk.', 100.00, 'coffee', 'Butterfly pea flower tea, Steamed milk', 'https://placeholder.svg?height=300&width=300', '2025-05-17 08:49:54'),
(17, 'Matcha Latte', 'Matcha green tea with steamed milk.', 110.00, 'coffee', 'Matcha powder, Steamed milk', 'https://placeholder.svg?height=300&width=300', '2025-05-17 08:49:54'),
(18, 'Blue Matcha', 'Blue matcha with steamed milk.', 115.00, 'coffee', 'Blue matcha powder, Steamed milk', 'https://placeholder.svg?height=300&width=300', '2025-05-17 08:49:54'),
(19, 'Strawberry Matcha', 'Matcha with strawberry flavor.', 120.00, 'coffee', 'Matcha powder, Strawberry syrup, Steamed milk', 'https://placeholder.svg?height=300&width=300', '2025-05-17 08:49:54'),
(20, 'Blueberry Matcha', 'Matcha with blueberry flavor.', 120.00, 'coffee', 'Matcha powder, Blueberry syrup, Steamed milk', 'https://placeholder.svg?height=300&width=300', '2025-05-17 08:49:54'),
(21, 'Caramel Matcha', 'Matcha with caramel flavor.', 120.00, 'coffee', 'Matcha powder, Caramel syrup, Steamed milk', 'https://placeholder.svg?height=300&width=300', '2025-05-17 08:49:54'),
(22, 'Strawberry Milk', 'Cold milk with strawberry flavor.', 100.00, 'coffee', 'Fresh milk, Strawberry syrup', 'https://placeholder.svg?height=300&width=300', '2025-05-17 08:49:54'),
(23, 'Blueberry Milk', 'Cold milk with blueberry flavor.', 100.00, 'coffee', 'Fresh milk, Blueberry syrup', 'https://placeholder.svg?height=300&width=300', '2025-05-17 08:49:54'),
(24, 'Mango Milk.', 'Cold milk with mango flavor.', 100.00, 'coffee', 'Fresh milk, Mango syrup', 'https://placeholder.svg?height=300&width=300', '2025-05-17 08:49:54'),
(25, 'Chocolate Croissant', 'Buttery croissant filled with rich chocolate.', 85.00, 'pastries', 'Flour, Butter, Chocolate, Sugar, Yeast, Salt', 'https://placeholder.svg?height=300&width=300', '2025-05-17 08:49:54'),
(26, 'Almond Croissant', 'Croissant filled with almond cream and topped with sliced almonds.', 95.00, 'pastries', 'Flour, Butter, Almond cream, Sliced almonds, Sugar, Yeast, Salt', 'https://placeholder.svg?height=300&width=300', '2025-05-17 08:49:54'),
(27, 'Cinnamon Roll', 'Soft roll with cinnamon sugar filling and cream cheese frosting.', 90.00, 'pastries', 'Flour, Butter, Cinnamon, Sugar, Cream cheese, Vanilla', 'https://placeholder.svg?height=300&width=300', '2025-05-17 08:49:54'),
(28, 'Blueberry Muffin', 'Moist muffin loaded with fresh blueberries.', 75.00, 'pastries', 'Flour, Butter, Blueberries, Sugar, Eggs, Milk, Baking powder', 'https://placeholder.svg?height=300&width=300', '2025-05-17 08:49:54'),
(29, 'Chocolate Chip Cookie', 'Classic cookie with chocolate chips.', 60.00, 'pastries', 'Flour, Butter, Chocolate chips, Brown sugar, White sugar, Eggs, Vanilla', 'https://placeholder.svg?height=300&width=300', '2025-05-17 08:49:54'),
(30, 'Red Velvet Cupcake', 'Moist red velvet cake with cream cheese frosting.', 85.00, 'pastries', 'Flour, Cocoa powder, Red food coloring, Butter, Sugar, Eggs, Cream cheese', 'https://placeholder.svg?height=300&width=300', '2025-05-17 08:49:54'),
(31, 'Cheese Danish', 'Flaky pastry with sweet cream cheese filling.', 90.00, 'pastries', 'Flour, Butter, Cream cheese, Sugar, Eggs, Vanilla', 'https://placeholder.svg?height=300&width=300', '2025-05-17 08:49:54'),
(32, 'Apple Turnover', 'Flaky pastry filled with cinnamon apple filling.', 85.00, 'pastries', 'Flour, Butter, Apples, Cinnamon, Sugar, Lemon juice', 'https://placeholder.svg?height=300&width=300', '2025-05-17 08:49:54'),
(33, 'Banana Bread Slice', 'Moist banana bread with walnuts.', 70.00, 'pastries', 'Flour, Bananas, Butter, Sugar, Eggs, Walnuts, Baking soda', 'https://placeholder.svg?height=300&width=300', '2025-05-17 08:49:54'),
(34, 'Lemon Tart', 'Buttery crust with tangy lemon filling.', 95.00, 'pastries', 'Flour, Butter, Eggs, Sugar, Lemons, Cream', 'https://placeholder.svg?height=300&width=300', '2025-05-17 08:49:54'),
(35, 'Chocolate Brownie', 'Rich, fudgy chocolate brownie.', 80.00, 'pastries', 'Flour, Butter, Chocolate, Sugar, Eggs, Vanilla', 'https://placeholder.svg?height=300&width=300', '2025-05-17 08:49:54'),
(36, 'Strawberry Cheesecake Slice', 'Creamy cheesecake with strawberry topping.', 110.00, 'pastries', 'Cream cheese, Graham crackers, Butter, Sugar, Eggs, Strawberries', 'https://placeholder.svg?height=300&width=300', '2025-05-17 08:49:54');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` enum('admin','staff','customer') NOT NULL DEFAULT 'customer',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `role`, `created_at`) VALUES
(1, 'ADMIN', '$2y$10$q3GZgodnX1.24wcWJ7Jae.wqLCS5gSlqQVTIbAz.8mp8N9e5EFBBa', 'admin@cafe.com', 'admin', '2025-05-17 08:47:52'),
(2, 'renzaquino7', '$2y$10$DloLUTCaPOXQzoShPqJ5luf/FdbxqTwHDxhoMDQ8xQFGqfxPJ8nce', 'renzaquino7@gmail.com', 'customer', '2025-05-17 08:51:57'),
(4, 'staff', '$2y$10$2RmMLlAs2iAx5znZ62mLVuWZKef.zUUpmAL7xeS0POQChKChqEG5y', 'staff@example.com', 'staff', '2025-05-17 10:52:09');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `customer_loyalty`
--
ALTER TABLE `customer_loyalty`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `loyalty_history`
--
ALTER TABLE `loyalty_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `customer_loyalty`
--
ALTER TABLE `customer_loyalty`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `loyalty_history`
--
ALTER TABLE `loyalty_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `customer_loyalty`
--
ALTER TABLE `customer_loyalty`
  ADD CONSTRAINT `customer_loyalty_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `inventory`
--
ALTER TABLE `inventory`
  ADD CONSTRAINT `inventory_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `loyalty_history`
--
ALTER TABLE `loyalty_history`
  ADD CONSTRAINT `loyalty_history_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
