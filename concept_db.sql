SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

DELIMITER $$

CREATE DEFINER=`root`@`localhost` PROCEDURE `addCustomerLoad` (IN `pr_customer_profile_id` INT(10), IN `pr_new_balance` DECIMAL(7,2), IN `pr_added_balance` DECIMAL(7,2))  BEGIN

	UPDATE customer_topups
    SET balance = pr_new_balance
    WHERE customer_profile_id = pr_customer_profile_id;
    
    INSERT INTO topup_transactions(customer_profile_id, amount, type)
    VALUES (pr_customer_profile_id, pr_added_balance, '1');

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `addItemToCart` (IN `pr_customer_profile_id` INTEGER(10), IN `pr_product_id` INTEGER(10), IN `pr_quantity` INTEGER(10))  BEGIN

	INSERT INTO customer_carts(customer_profile_id, product_id, quantity)
    VALUES (pr_customer_profile_id, pr_product_id, pr_quantity);

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `addToDeliveryItems` (IN `pr_delivery_id` INTEGER(10), IN `pr_product_id` INTEGER(10), IN `pr_quantity` INTEGER(10), IN `pr_price` DECIMAL(7,2))  BEGIN

	INSERT INTO delivery_items(delivery_id, product_id, quantity, price)
    VALUES (pr_delivery_id, pr_product_id, pr_quantity, pr_price);

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `AddToWishlist` (IN `pr_customid` INT(10), IN `pr_prodid` INT(10))  NO SQL
BEGIN

INSERT INTO `wish_list`(`isactive`, `customer_profile_id`, `product_id`) VALUES (1, pr_customid, pr_prodid);


END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `authenticateCustomerProfile` (IN `pr_email` VARCHAR(191), IN `pr_password` VARCHAR(191))  BEGIN

	SELECT customer_profiles.id AS profile_id, customer_profiles.first_name, customer_profiles.last_name
    FROM customer_profiles
    JOIN users
    ON customer_profiles.user_id = users.id
    WHERE users.email = pr_email AND users.password = pr_password;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `checkEmailAvailability` (IN `pr_email` VARCHAR(191))  BEGIN

	SELECT email
    FROM users WHERE email=pr_email;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `checkItemInCart` (IN `pr_cart_product_id` INT(10), IN `pr_customer_profile_id` INT(10))  BEGIN

	SELECT customer_carts.id, customer_carts.quantity, products.stock
    FROM customer_carts
    JOIN products
    ON products.id = customer_carts.product_id
    WHERE customer_carts.product_id = pr_cart_product_id
    AND customer_carts.customer_profile_id = pr_customer_profile_id
    AND customer_carts.isActive = '1';

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `createDeliveryReference` (IN `pr_customer_profile_id` INT(10), IN `pr_contact_person` VARCHAR(191), IN `pr_location` VARCHAR(191), IN `pr_contact_number` VARCHAR(11), IN `pr_payment_type` VARCHAR(1), IN `pr_added` TIMESTAMP, IN `pr_arrival_time` TIMESTAMP)  BEGIN
	
    INSERT INTO deliveries(customer_profile_id, contact_person, location, contact_number, payment_type, added, arrival_date)
    VALUES (pr_customer_profile_id, pr_contact_person, pr_location, pr_contact_number, pr_payment_type, pr_added, pr_arrival_time);
    
    SELECT LAST_INSERT_ID() as delivery_id;
    
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `deleteFromCart` (IN `pr_cart_id` INT(10))  BEGIN

	UPDATE customer_carts
    SET isActive = '0'
    WHERE id = pr_cart_id;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getAllProducts` ()  BEGIN

SELECT products.id, products.name, products.category, products.stock, products.price, shop_profiles.shop_name, product_pictures.image_location
    FROM products 
    JOIN shop_profiles 
    ON shop_profiles.id = products.shop_profile_id 
    JOIN product_pictures 
    ON product_pictures.product_id = products.id 
    WHERE product_pictures.image_location LIKE '%_0.%'
    ORDER BY products.id DESC ;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getCartCount` (IN `pr_customer_profile_id` INT(10))  BEGIN

	SELECT COUNT(id) AS cart_items
    FROM customer_carts
    WHERE customer_profile_id = pr_customer_profile_id
    AND isActive = '1';

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getCartItemsInformation` (IN `pr_customer_profile_id` INT(10))  BEGIN

	SELECT customer_carts.id AS cart_id, products.id AS product_id, products.price, products.name, shop_profiles.shop_name, customer_carts.quantity,
    products.stock, product_pictures.image_location
    FROM customer_carts
    JOIN products
    ON products.id = customer_carts.product_id
    JOIN shop_profiles
    ON shop_profiles.id = products.shop_profile_id
    JOIN product_pictures
    ON product_pictures.product_id = products.id 
    WHERE product_pictures.image_location LIKE '%_0.%'
    AND customer_carts.customer_profile_id = pr_customer_profile_id
AND customer_carts.isActive = '1'
ORDER BY customer_carts.id DESC;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getCurrentBalance` (IN `pr_customer_profile_id` INTEGER(10))  BEGIN

	SELECT balance
    FROM customer_topups
    WHERE customer_profile_id = pr_customer_profile_id;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getDeliveryItems` (IN `pr_customer_profile_id` INT(10))  BEGIN

	SELECT delivery_items.id, products.name, shop_profiles.shop_name, delivery_items.quantity, delivery_items.price, product_pictures.image_location, delivery_items.status, deliveries.added, deliveries.arrival_date, deliveries.contact_person, deliveries.location, deliveries.contact_number
    
    FROM deliveries
    JOIN delivery_items
    ON delivery_items.delivery_id = deliveries.id
    JOIN products
    ON delivery_items.product_id = products.id
    JOIN product_pictures
    ON products.id = product_pictures.product_id
    JOIN shop_profiles
    ON shop_profiles.id = products.shop_profile_id
    WHERE deliveries.customer_profile_id = pr_customer_profile_id
    AND product_pictures.image_location LIKE '%_0%'
    ORDER BY deliveries.added DESC;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getLoadTransactions` (IN `pr_customer_profile_id` INTEGER(10))  BEGIN

	SELECT * FROM topup_transactions
    WHERE customer_profile_id = pr_customer_profile_id
    ORDER BY transaction_date DESC;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getProductImageAddresses` (IN `pr_product_id` INTEGER(10))  BEGIN

	SELECT image_location
	FROM product_pictures
    WHERE product_id = pr_product_id;
    
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getProductInformation` (IN `pr_product_id` INTEGER(10))  BEGIN

	SELECT products.name, products.description, products.price, products.stock, shop_profiles.shop_name
    FROM products
    JOIN shop_profiles
    ON products.shop_profile_id = shop_profiles.id
    WHERE products.id = pr_product_id;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getProductViewProducts` (IN `pr_customer_profile_id` INT(10))  BEGIN

	SELECT products.id, products.name, products.price, shop_profiles.shop_name, product_pictures.image_location
    FROM products 
    JOIN customer_profiles 
    ON (customer_profiles.gender = products.gender OR products.gender = 'U') 
    JOIN shop_profiles 
    ON shop_profiles.id = products.shop_profile_id 
    JOIN product_pictures 
    ON product_pictures.product_id = products.id 
    WHERE customer_profiles.id = pr_customer_profile_id AND product_pictures.image_location LIKE '%_0.%'
    ORDER BY products.id DESC ;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getProfileInformation` (IN `pr_profile_id` INT(10))  BEGIN

	SELECT customer_profiles.first_name, customer_profiles.last_name, customer_profiles.address, customer_profiles.contact_number, users.email
    FROM customer_profiles
    JOIN users
    ON customer_profiles.user_id = users.id
    WHERE customer_profiles.id = pr_profile_id;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `registerCustomerAccount` (IN `pr_first_name` VARCHAR(35), IN `pr_last_name` VARCHAR(35), IN `pr_gender` VARCHAR(1), IN `pr_address` VARCHAR(191), IN `pr_contact_number` VARCHAR(11), IN `pr_email` VARCHAR(191), IN `pr_password` VARCHAR(191))  BEGIN

	INSERT INTO users(email, password, user_type)
    VALUES (pr_email, pr_password, 2);
    
    INSERT INTO customer_profiles(first_name, last_name, gender, address, contact_number, user_id)
    VALUES (pr_first_name, pr_last_name, pr_gender, pr_address, pr_contact_number, LAST_INSERT_ID());
    
    SELECT LAST_INSERT_ID() as customer_id;
    
    INSERT INTO customer_topups(customer_profile_id)
    VALUES (LAST_INSERT_ID());

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `updateCartProcessedInfo` (IN `pr_cart_id` INTEGER(10), IN `pr_process_date` TIMESTAMP)  BEGIN

	UPDATE customer_carts
    SET isProcessed = '1', processedIn = pr_process_date
    WHERE id = pr_cart_id;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `updateCartQuantity` (IN `pr_cart_id` INTEGER(10), IN `pr_new_quantity` INTEGER(10))  BEGIN

	UPDATE customer_carts
    SET quantity = pr_new_quantity
    WHERE id = pr_cart_id;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `updateCustomerLoad` (IN `pr_customer_profile_id` INT(10), IN `pr_prev_balance` DECIMAL(7,2), IN `pr_added_balance` DECIMAL(7,2), IN `pr_new_balance` DECIMAL(7,2))  BEGIN

	UPDATE customer_topups
    SET balance = pr_new_balance
    WHERE customer_profile_id = pr_customer_profile_id;
    
    INSERT INTO topup_transactions(customer_profile_id, amount, type)
    VALUES (pr_customer_profile_id, pr_added_balance, '2');

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `updateCustomerPassword` (IN `pr_email` VARCHAR(191), IN `pr_new_password` VARCHAR(191))  BEGIN

	UPDATE users
    SET password = pr_new_password
    WHERE email = pr_email;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `updateCustomerProfile` (IN `pr_first_name` VARCHAR(191), IN `pr_last_name` VARCHAR(191), IN `pr_email` VARCHAR(191), IN `pr_address` VARCHAR(191), IN `pr_contact_number` VARCHAR(11), IN `pr_profile_id` INT(10))  BEGIN

	UPDATE  customer_profiles
    SET first_name = pr_first_name, last_name = pr_last_name, address = pr_address,
    contact_number = pr_contact_number
    WHERE id = pr_profile_id;
    
    /* UPDATE users JOIN customer_profiles
    ON customer_profiles.user_id = users.id
    SET users.email = pr_email; */
    
    

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `updateOrderStatus` (IN `pr_delivery_item_id` INTEGER(10), IN `pr_new_status` VARCHAR(1))  BEGIN

	UPDATE delivery_items
    SET status = pr_new_status
    WHERE id = pr_delivery_item_id;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `updateProductQuantity` (IN `pr_product_id` INTEGER(10), IN `pr_new_quantity` INTEGER(10))  BEGIN

	UPDATE products
    SET stock = pr_new_quantity
	WHERE id = pr_product_id;
    
END$$

DELIMITER ;

CREATE TABLE `customer_carts` (
  `id` int(10) UNSIGNED NOT NULL,
  `customer_profile_id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL,
  `isActive` varchar(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `isProcessed` varchar(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `addedIn` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `processedIn` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `customer_profiles` (
  `id` int(10) UNSIGNED NOT NULL,
  `first_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `gender` varchar(1) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_number` varchar(11) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `customer_topups` (
  `id` int(10) UNSIGNED NOT NULL,
  `customer_profile_id` int(10) UNSIGNED NOT NULL,
  `balance` decimal(7,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `deliveries` (
  `id` int(10) UNSIGNED NOT NULL,
  `customer_profile_id` int(10) UNSIGNED NOT NULL,
  `contact_person` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `location` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_number` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_type` varchar(1) COLLATE utf8mb4_unicode_ci NOT NULL,
  `added` timestamp NULL DEFAULT NULL,
  `arrival_date` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `delivery_items` (
  `id` int(10) UNSIGNED NOT NULL,
  `delivery_id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `quantity` int(10) UNSIGNED NOT NULL,
  `price` decimal(7,2) NOT NULL,
  `status` varchar(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `password_resets` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `products` (
  `id` int(10) UNSIGNED NOT NULL,
  `shop_profile_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(35) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `gender` varchar(1) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(7,2) NOT NULL,
  `stock` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `product_pictures` (
  `id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `image_location` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `shop_profiles` (
  `id` int(10) UNSIGNED NOT NULL,
  `shop_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `shop_description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `shop_location` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `topup_transactions` (
  `id` int(10) UNSIGNED NOT NULL,
  `customer_profile_id` int(10) UNSIGNED NOT NULL,
  `amount` decimal(7,2) NOT NULL,
  `type` varchar(1) COLLATE utf8mb4_unicode_ci NOT NULL,
  `transaction_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_type` int(10) UNSIGNED NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `user_types` (
  `id` int(10) UNSIGNED NOT NULL,
  `type_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type_description` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `wish_list` (
  `isactive` int(11) NOT NULL,
  `customer_profile_id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `customer_carts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_carts_customer_profile_id_product_id_index` (`customer_profile_id`,`product_id`),
  ADD KEY `customer_carts_product_id_foreign` (`product_id`);
  
ALTER TABLE `customer_profiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `customer_profiles_user_id_unique` (`user_id`),
  ADD KEY `customer_profiles_id_index` (`id`);
  
ALTER TABLE `customer_topups`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_topups_customer_profile_id_index` (`customer_profile_id`);
  
ALTER TABLE `deliveries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `deliveries_customer_profile_id_index` (`customer_profile_id`);
  
ALTER TABLE `delivery_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `delivery_items_delivery_id_index` (`delivery_id`),
  ADD KEY `delivery_items_product_id_foreign` (`product_id`);
  
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);
  
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);
  
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `products_shop_id_index` (`shop_profile_id`);
  
ALTER TABLE `product_pictures`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_pictures_product_id_index` (`product_id`);
  
ALTER TABLE `shop_profiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `shop_profiles_shop_name_unique` (`shop_name`),
  ADD UNIQUE KEY `shop_profiles_user_id_unique` (`user_id`),
  ADD KEY `shop_profiles_id_index` (`id`);
  
ALTER TABLE `topup_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `topup_transactions_customer_profile_id_index` (`customer_profile_id`);
  
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_id_profile_id_user_type_index` (`id`,`user_type`),
  ADD KEY `users_user_type_foreign` (`user_type`);
  
ALTER TABLE `user_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_types_id_unique` (`id`),
  ADD KEY `user_types_id_index` (`id`);
  
ALTER TABLE `wish_list`
  ADD KEY `customer_profile_id` (`customer_profile_id`),
  ADD KEY `product_id` (`product_id`);
  
ALTER TABLE `customer_carts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;
  
ALTER TABLE `customer_profiles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
  
ALTER TABLE `customer_topups`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
  
ALTER TABLE `deliveries`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;
  
ALTER TABLE `delivery_items`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;
  
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;
  
ALTER TABLE `products`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
  
ALTER TABLE `product_pictures`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;
  
ALTER TABLE `shop_profiles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
  
ALTER TABLE `topup_transactions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;
  
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
  
ALTER TABLE `customer_carts`
  ADD CONSTRAINT `customer_carts_customer_profile_id_foreign` FOREIGN KEY (`customer_profile_id`) REFERENCES `customer_profiles` (`id`),
  ADD CONSTRAINT `customer_carts_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);
  
ALTER TABLE `customer_profiles`
  ADD CONSTRAINT `customer_profiles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
  
ALTER TABLE `customer_topups`
  ADD CONSTRAINT `customer_topups_customer_profile_id_foreign` FOREIGN KEY (`customer_profile_id`) REFERENCES `customer_profiles` (`id`);
  
ALTER TABLE `deliveries`
  ADD CONSTRAINT `deliveries_customer_profile_id_foreign` FOREIGN KEY (`customer_profile_id`) REFERENCES `customer_profiles` (`id`);
  
ALTER TABLE `delivery_items`
  ADD CONSTRAINT `delivery_items_delivery_id_foreign` FOREIGN KEY (`delivery_id`) REFERENCES `deliveries` (`id`),
  ADD CONSTRAINT `delivery_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);
  
ALTER TABLE `products`
  ADD CONSTRAINT `products_shop_id_foreign` FOREIGN KEY (`shop_profile_id`) REFERENCES `shop_profiles` (`id`);
  
ALTER TABLE `product_pictures`
  ADD CONSTRAINT `product_pictures_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);
  
ALTER TABLE `shop_profiles`
  ADD CONSTRAINT `shop_profiles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
  
ALTER TABLE `topup_transactions`
  ADD CONSTRAINT `topup_transactions_customer_profile_id_foreign` FOREIGN KEY (`customer_profile_id`) REFERENCES `customer_profiles` (`id`);
  
ALTER TABLE `users`
  ADD CONSTRAINT `users_user_type_foreign` FOREIGN KEY (`user_type`) REFERENCES `user_types` (`id`);
  
ALTER TABLE `wish_list`
  ADD CONSTRAINT `wish_list_ibfk_1` FOREIGN KEY (`customer_profile_id`) REFERENCES `customer_profiles` (`id`),
  ADD CONSTRAINT `wish_list_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);
COMMIT;

