CREATE TABLE IF NOT EXISTS `countries` (
                                           `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                                           `code` CHAR(2) NOT NULL UNIQUE COMMENT 'Код страны (IT, FR, DE, ES и т.д.)',
    `name` VARCHAR(100) NOT NULL COMMENT 'Название страны',
    `phone_code` VARCHAR(10) NULL COMMENT 'Телефонный код (+39, +33 и т.д.)',
    `currency` CHAR(3) NULL COMMENT 'Валюта страны по умолчанию',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,

    INDEX `idx_code` (`code`),
    INDEX `idx_name` (`name`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Справочник стран';

CREATE TABLE IF NOT EXISTS `orders` (
                                        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                                        `hash` CHAR(32) NOT NULL COMMENT 'MD5 хеш заказа',
    `user_id` INT UNSIGNED NULL,
    `token` VARCHAR(64) NOT NULL COMMENT 'Хеш пользователя',
    `number` VARCHAR(10) NULL COMMENT 'Номер заказа',

    -- Статусы
    `status` TINYINT NOT NULL DEFAULT 1 COMMENT '1-новый,2-оплачен,3-собран,4-отправлен,5-доставлен,6-отменен',
    `step` TINYINT NOT NULL DEFAULT 1 COMMENT 'Шаг оформления',

    -- Клиент (только ключи)
    `client_name` VARCHAR(255) NULL,
    `client_surname` VARCHAR(255) NULL,
    `email` VARCHAR(100) NULL,
    `company_name` VARCHAR(255) NULL,

    -- Финансы (только ключевые поля)
    `currency` CHAR(3) NOT NULL DEFAULT 'EUR',
    `cur_rate` DECIMAL(10,4) NOT NULL DEFAULT 1.0000,
    `total_amount` DECIMAL(12,2) NULL COMMENT 'Общая сумма заказа',
    `discount` SMALLINT NULL,

    -- Временные метки (для партицирования)
    `create_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `update_date` DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` DATETIME NULL,

    -- Индексы
    INDEX `idx_user_id` (`user_id`),
    INDEX `idx_hash` (`hash`),
    INDEX `idx_number` (`number`),
    INDEX `idx_status` (`status`),
    INDEX `idx_create_date` (`create_date`),
    INDEX `idx_status_date` (`status`, `create_date`),

    -- Внешний ключ
    CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL

    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    COMMENT='Основная информация о заказах'
    );


CREATE TABLE IF NOT EXISTS `order_delivery` (
                                                `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                                                `order_id` INT UNSIGNED NOT NULL UNIQUE,

    -- Стоимость
                                                `delivery_cost` DECIMAL(10,2) NULL COMMENT 'Стоимость доставки',
    `delivery_cost_eur` DECIMAL(10,2) NULL,
    `delivery_type` TINYINT NULL DEFAULT 0 COMMENT '0-адрес клиента,1-склад',
    `calculate_type` TINYINT NULL DEFAULT 0 COMMENT '0-ручной,1-автомат',

    -- Адрес
    `country_id` INT UNSIGNED NULL,
    `region` VARCHAR(50) NULL,
    `city` VARCHAR(200) NULL,
    `postal_index` VARCHAR(20) NULL,
    `address_line` VARCHAR(300) NULL,
    `building` VARCHAR(200) NULL,
    `apartment` VARCHAR(30) NULL,
    `phone_code` VARCHAR(20) NULL,
    `phone` VARCHAR(20) NULL,

    -- Сроки
    `time_min` DATE NULL,
    `time_max` DATE NULL,
    `time_confirm_min` DATE NULL,
    `time_confirm_max` DATE NULL,
    `time_fast_pay_min` DATE NULL,
    `time_fast_pay_max` DATE NULL,
    `time_old_min` DATE NULL,
    `time_old_max` DATE NULL,

    -- Логистика
    `tracking_number` VARCHAR(50) NULL,
    `carrier_name` VARCHAR(50) NULL,
    `carrier_contacts` VARCHAR(255) NULL,
    `weight_gross` DECIMAL(10,2) NULL,

    -- Даты
    `proposed_date` DATETIME NULL,
    `ship_date` DATETIME NULL,
    `fact_date` DATETIME NULL,
    `cancel_date` DATETIME NULL,

    -- Сдвиги
    `offset_date` DATETIME NULL,
    `offset_reason` TINYINT NULL COMMENT '1-каникулы,2-уточнение,3-другое',

    `updated_at` DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,

    INDEX `idx_order_id` (`order_id`),
    INDEX `idx_country` (`country_id`),
    INDEX `idx_tracking` (`tracking_number`),
    INDEX `idx_delivery_dates` (`time_min`, `time_max`),

    CONSTRAINT `fk_delivery_order` FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_delivery_country` FOREIGN KEY (`country_id`) REFERENCES `countries`(`id`) ON DELETE SET NULL

    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    COMMENT='Детали доставки заказа';

CREATE TABLE IF NOT EXISTS `order_payment` (
                                               `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                                               `order_id` INT UNSIGNED NOT NULL UNIQUE,

                                               `pay_type` TINYINT NOT NULL COMMENT '1-карта,2-перевод,3-наличные',
                                               `pay_date_execution` DATETIME NULL COMMENT 'Срок действия цены',
                                               `full_payment_date` DATE NULL,
                                               `bank_transfer_requested` TINYINT(1) NULL,
    `accept_pay` TINYINT(1) NULL,
    `payment_euro` TINYINT(1) NOT NULL DEFAULT 0,
    `spec_price` TINYINT(1) NULL,

    -- Налоги
    `vat_type` TINYINT NOT NULL DEFAULT 0 COMMENT '0-физ.лицо,1-плательщик НДС',
    `vat_number` VARCHAR(100) NULL,
    `tax_number` VARCHAR(50) NULL,

    -- Реквизиты для возврата (JSON - не нужно индексировать)
    `bank_details` JSON NULL COMMENT 'Реквизиты банка для возврата',

    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,

    INDEX `idx_order_id` (`order_id`),
    INDEX `idx_pay_type` (`pay_type`),
    INDEX `idx_payment_date` (`full_payment_date`),

    CONSTRAINT `fk_payment_order` FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE

    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    COMMENT='Платежная информация заказа';

CREATE TABLE IF NOT EXISTS `order_management` (
                                                  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                                                  `order_id` INT UNSIGNED NOT NULL UNIQUE,

    -- Менеджер
                                                  `manager_name` VARCHAR(20) NULL,
    `manager_email` VARCHAR(30) NULL,
    `manager_phone` VARCHAR(20) NULL,

    -- Адрес плательщика
    `address_equal` TINYINT(1) NOT NULL DEFAULT 1,
    `address_payer_id` INT UNSIGNED NULL,

    -- Технические метки
    `mirror` TINYINT NULL COMMENT 'Метка зеркала',
    `process` TINYINT(1) NULL COMMENT 'Массовая обработка',
    `show_msg` TINYINT(1) NULL,
    `product_review` TINYINT(1) NULL,
    `entrance_review` TINYINT NULL,

    -- Доп.данные
    `warehouse_data` JSON NULL,
    `description` TEXT NULL,

    `updated_at` DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,

    INDEX `idx_order_id` (`order_id`),
    INDEX `idx_manager` (`manager_name`),
    INDEX `idx_mirror` (`mirror`),

    CONSTRAINT `fk_management_order` FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE

    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    COMMENT='Управленческая информация';

CREATE TABLE IF NOT EXISTS `order_items` (
                                             `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                                             `order_id` INT UNSIGNED NOT NULL,
                                             `article_id` INT UNSIGNED NOT NULL,
                                             `article_sku` VARCHAR(100) NULL,

    `amount` DECIMAL(10,3) NOT NULL DEFAULT 0.000,
    `packaging_count` DECIMAL(10,3) NOT NULL DEFAULT 1.000,
    `weight` DECIMAL(10,3) NOT NULL,
    `packaging` DECIMAL(10,3) NOT NULL,
    `pallet` DECIMAL(10,3) NOT NULL,

    `price` DECIMAL(12,2) NOT NULL,
    `price_eur` DECIMAL(12,2) NULL,
    `currency` CHAR(3) NULL,
    `measure` CHAR(2) NULL,

    `delivery_time_min` DATE NULL,
    `delivery_time_max` DATE NULL,
    `multiple_pallet` TINYINT NULL,
    `swimming_pool` TINYINT(1) NOT NULL DEFAULT 0,

    INDEX `idx_order_id` (`order_id`),
    INDEX `idx_article_id` (`article_id`),
    INDEX `idx_order_article` (`order_id`, `article_id`),

    CONSTRAINT `fk_items_order` FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE

    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    COMMENT='Товары в заказе';


CREATE TABLE IF NOT EXISTS `order_status_history` (
                                                      `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                                                      `order_id` INT UNSIGNED NOT NULL,
                                                      `old_status` TINYINT NOT NULL,
                                                      `new_status` TINYINT NOT NULL,
                                                      `changed_by` INT UNSIGNED NULL,
                                                      `comment` VARCHAR(500) NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    INDEX `idx_order_id` (`order_id`),
    INDEX `idx_created_at` (`created_at`),
    INDEX `idx_status_change` (`old_status`, `new_status`),

    CONSTRAINT `fk_history_order` FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_history_user` FOREIGN KEY (`changed_by`) REFERENCES `users`(`id`) ON DELETE SET NULL

    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    COMMENT='История изменения статусов';
