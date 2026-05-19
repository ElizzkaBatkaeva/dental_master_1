-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Май 19 2026 г., 23:47
-- Версия сервера: 10.1.44-MariaDB
-- Версия PHP: 7.1.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `dental_master`
--

-- --------------------------------------------------------

--
-- Структура таблицы `appointment`
--

CREATE TABLE `appointment` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` varchar(10) NOT NULL,
  `status` varchar(20) DEFAULT 'pending',
  `total_price` int(11) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `appointment`
--

INSERT INTO `appointment` (`id`, `patient_id`, `doctor_id`, `service_id`, `appointment_date`, `appointment_time`, `status`, `total_price`, `created_at`) VALUES
(5, 1, 2, 43, '2026-05-14', '12:00', 'confirmed', 23000, '2026-05-14 15:23:14'),
(6, 1, 2, 32, '2026-05-14', '16:00', 'confirmed', 38000, '2026-05-14 15:26:13'),
(7, 1, 2, 21, '2026-05-14', '15:00', 'pending', 6000, '2026-05-14 15:29:36'),
(8, 1, 8, 42, '2026-05-14', '16:00', 'pending', 28000, '2026-05-14 15:30:11'),
(9, 2, 2, 10, '2026-05-15', '11:00', 'pending', 5800, '2026-05-14 16:44:25'),
(10, 22, 2, 1, '2026-05-18', '10:00', 'completed', 850, '2026-05-17 14:28:57'),
(11, 22, 2, 2, '2026-05-17', '09:00', 'pending', 600, '2026-05-17 14:38:15'),
(12, 23, 2, 2, '2026-05-17', '16:00', 'completed', 600, '2026-05-17 18:02:35'),
(13, 2, 7, 10, '2026-05-17', '15:00', 'pending', 5800, '2026-05-17 18:35:45'),
(14, 6, 11, 8, '2026-05-17', '15:00', 'completed', 3500, '2026-05-17 20:23:34'),
(15, 24, 18, 1, '2026-05-18', '09:30', 'completed', 850, '2026-05-18 08:13:09');

-- --------------------------------------------------------

--
-- Структура таблицы `review`
--

CREATE TABLE `review` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` tinyint(4) NOT NULL,
  `advantages` text,
  `disadvantages` text,
  `comment` text NOT NULL,
  `status` varchar(20) DEFAULT 'pending',
  `admin_response` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `review`
--

INSERT INTO `review` (`id`, `user_id`, `rating`, `advantages`, `disadvantages`, `comment`, `status`, `admin_response`, `created_at`, `updated_at`) VALUES
(1, 1, 5, 'Профессионализм, чистота, современное оборудование', 'Нет', 'Отличная клиника! Лечил кариес у доктора Ветровой. Всё быстро, качественно и без боли. Обязательно вернусь на имплантацию.', 'approved', 'Согласен', '2026-05-17 14:59:26', '2026-05-17 15:00:20'),
(3, 23, 5, 'пп', 'п', 'пп', 'rejected', NULL, '2026-05-17 18:03:48', '2026-05-17 18:04:12'),
(4, 6, 5, '+', '_', 'Обязательный текст для отзыва', 'pending', NULL, '2026-05-17 20:25:23', '2026-05-17 20:25:23'),
(5, 24, 4, 'ув', 'у3чу', '3чук3кч', 'rejected', NULL, '2026-05-18 08:14:23', '2026-05-18 08:14:40');

-- --------------------------------------------------------

--
-- Структура таблицы `service`
--

CREATE TABLE `service` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `category` varchar(50) NOT NULL,
  `price` int(11) NOT NULL,
  `duration` int(11) DEFAULT '30',
  `description` text,
  `icon` varchar(50) DEFAULT 'fa-tooth',
  `is_active` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `service`
--

INSERT INTO `service` (`id`, `name`, `category`, `price`, `duration`, `description`, `icon`, `is_active`) VALUES
(1, 'Осмотр и консультация стоматолога', 'diagnostics', 850, 30, 'Первичный осмотр, консультация, составление плана лечения', 'fa-stethoscope', 1),
(2, 'Повторная консультация', 'diagnostics', 600, 20, 'Повторный прием, корректировка плана лечения', 'fa-user-md', 1),
(3, 'Панорамный снимок (ОПТГ)', 'diagnostics', 1500, 10, 'Ортопантомограмма – обзорный снимок всех зубов', 'fa-camera', 1),
(4, '3D-снимок (КТ челюсти)', 'diagnostics', 2800, 15, 'Конусно-лучевая томография, 3D-модель', 'fa-cube', 1),
(5, 'Прицельный рентгеновский снимок', 'diagnostics', 600, 5, '1 зуб (периапикальный снимок)', 'fa-image', 1),
(6, 'Телерентгенограмма (ТРГ)', 'diagnostics', 2200, 20, 'Боковая проекция черепа для ортодонтии', 'fa-chart-simple', 1),
(7, 'Комплексная диагностика', 'diagnostics', 5500, 60, 'КТ + ОПТГ + консультация + 3D-план лечения', 'fa-chart-line', 1),
(8, 'Лечение кариеса (1 поверхность)', 'therapy', 3500, 40, 'Пломбирование световым композитом', 'fa-tooth', 1),
(9, 'Лечение кариеса (2 поверхности)', 'therapy', 4500, 50, 'Средний кариес, восстановление контактного пункта', 'fa-tooth', 1),
(10, 'Лечение кариеса (3 и более поверхностей)', 'therapy', 5800, 60, 'Глубокий кариес, сложная полость', 'fa-tooth', 1),
(11, 'Лечение пульпита (1 корневой канал)', 'therapy', 6500, 60, 'Эндодонтическое лечение, пломбирование', 'fa-microscope', 1),
(12, 'Лечение пульпита (2 канала)', 'therapy', 8500, 75, 'Механическая и медикаментозная обработка', 'fa-microscope', 1),
(13, 'Лечение пульпита (3 канала)', 'therapy', 10500, 90, 'Сложная эндодонтия', 'fa-microscope', 1),
(14, 'Лечение периодонтита (1 канал)', 'therapy', 7800, 70, 'Хроническое воспаление за верхушкой корня', 'fa-microscope', 1),
(15, 'Лечение под микроскопом (1 канал)', 'therapy', 9500, 80, 'Эндодонтия под увеличением', 'fa-microscope', 1),
(16, 'Эстетическая реставрация (фронтальный зуб)', 'therapy', 7500, 60, 'Художественная реставрация переднего зуба', 'fa-paintbrush', 1),
(17, 'Эстетическая реставрация (жевательный зуб)', 'therapy', 5800, 50, 'Восстановление жевательной группы', 'fa-paintbrush', 1),
(18, 'Снятие старой пломбы + лечение', 'therapy', 3000, 45, 'Замена некачественной пломбы', 'fa-rotate-right', 1),
(19, 'Внутриканальное отбеливание (1 зуб)', 'therapy', 4500, 50, 'Отбеливание депульпированного зуба изнутри', 'fa-sun', 1),
(20, 'Удаление зуба (простое, однокорневой)', 'surgery', 3500, 30, 'Неосложненное удаление', 'fa-tooth', 1),
(21, 'Удаление зуба (сложное, многокорневой)', 'surgery', 6000, 50, 'Распил корней, извлечение фрагментов', 'fa-tooth', 1),
(22, 'Удаление зуба мудрости (простое)', 'surgery', 5500, 45, 'Правильно прорезавшийся, без ретенции', 'fa-skull', 1),
(23, 'Удаление зуба мудрости (сложное, ретинированный)', 'surgery', 10000, 75, 'Горизонтальное положение, глубокое расположение', 'fa-skull', 1),
(24, 'Удаление ретинированного зуба (не мудрости)', 'surgery', 8500, 65, 'Клык или премоляр в кости', 'fa-tooth', 1),
(25, 'Резекция верхушки корня', 'surgery', 8500, 60, 'Микрохирургическая операция', 'fa-microscope', 1),
(26, 'Гемисекция корня', 'surgery', 6500, 50, 'Удаление одного корня многокорневого зуба', 'fa-cut', 1),
(27, 'Цистэктомия (удаление кисты)', 'surgery', 7500, 55, 'Вместе с удалением причинного зуба или сохранением', 'fa-circle-nodes', 1),
(28, 'Вскрытие абсцесса (периостит, флюс)', 'surgery', 4000, 40, 'Дренирование, лечение острого воспаления', 'fa-syringe', 1),
(29, 'Пластика уздечки губы или языка', 'surgery', 5500, 30, 'Лазерная или скальпельная пластика', 'fa-scissors', 1),
(30, 'Иссечение капюшона над зубом мудрости', 'surgery', 4500, 25, 'Лазерная операция при перикороните', 'fa-scissors', 1),
(31, 'Установка импланта (бюджетный, Корея)', 'implants', 28000, 60, 'Osstem, Implantium и аналоги', 'fa-microscope', 1),
(32, 'Установка импланта (средний сегмент, Израиль)', 'implants', 38000, 60, 'Alpha Bio, MIS, Adin', 'fa-microscope', 1),
(33, 'Установка импланта (премиум, Европа)', 'implants', 50000, 60, 'Straumann (Швейцария), Nobel Biocare (США/Швеция)', 'fa-gem', 1),
(34, 'Установка импланта (немецкий)', 'implants', 45000, 60, 'Bego, Xive, ICX', 'fa-gem', 1),
(35, 'Формирователь десны', 'implants', 5500, 30, 'Установка формирователя через 3-6 месяцев', 'fa-tooth', 1),
(36, 'Установка абатмента (стандартный)', 'implants', 8000, 30, 'Титановый абатмент', 'fa-plug', 1),
(37, 'Установка абатмента (индивидуальный, CAD/CAM)', 'implants', 15000, 45, 'Циркониевый или титановый индивидуальный', 'fa-plug', 1),
(38, 'Имплантация под ключ (имплант + коронка)', 'implants', 75000, 120, 'Имплант (средний) + абатмент + коронка', 'fa-crown', 1),
(39, 'Одномоментная имплантация', 'implants', 52000, 90, 'Удаление + имплант в ту же лунку', 'fa-bolt', 1),
(40, 'Коронка металлокерамическая', 'prosthetics', 9500, 90, '2 визита, бюджетный вариант', 'fa-crown', 1),
(41, 'Коронка металлокерамическая (на импланте)', 'prosthetics', 12500, 90, 'Винтовая фиксация', 'fa-crown', 1),
(42, 'Коронка из диоксида циркония', 'prosthetics', 28000, 120, 'Эстетика, биосовместимость', 'fa-crown', 1),
(43, 'Коронка из E-max (прессованная керамика)', 'prosthetics', 23000, 120, 'Максимальная эстетика для фронтальной группы', 'fa-crown', 1),
(44, 'Коронка цельнолитая (хром-кобальт)', 'prosthetics', 5500, 90, 'Бюджетный вариант для жевательных зубов', 'fa-crown', 1),
(45, 'Винир керамический (1 зуб)', 'prosthetics', 19000, 90, 'Эстетика без сильной обточки', 'fa-smile', 1),
(46, 'Винир композитный (прямой, 1 зуб)', 'prosthetics', 6500, 60, 'Художественная реставрация виниром', 'fa-smile', 1),
(47, 'Люминир (ультратонкий винир)', 'prosthetics', 28000, 90, 'Без обточки зуба, только по показаниям', 'fa-sun', 1),
(48, 'Мостовидный протез (3 единицы, металлокерамика)', 'prosthetics', 35000, 180, 'Из 3-х коронок', 'fa-bridge', 1),
(49, 'Мостовидный протез (3 единицы, цирконий)', 'prosthetics', 75000, 180, 'Цельнолитой каркас из циркония', 'fa-bridge', 1),
(50, 'Бюгельный протез (с кламмерами)', 'prosthetics', 42000, 150, 'Частичный съемный протез из нейлона или акрила', 'fa-hand-peace', 1),
(51, 'Полный съемный протез (акрил)', 'prosthetics', 27000, 120, 'При полной адентии на одну челюсть', 'fa-hand-peace', 1),
(52, 'Профессиональная гигиена (ультразвук + AirFlow)', 'hygiene', 4200, 60, 'Снятие камня + пескоструйная чистка + полировка', 'fa-spray-can-sparkles', 1),
(53, 'AirFlow (пескоструйная чистка)', 'hygiene', 2400, 30, 'Снятие пигментированного налета', 'fa-wind', 1),
(54, 'Ультразвуковая чистка (весь рот)', 'hygiene', 2800, 40, 'Снятие наддесневого и поддесневого камня', 'fa-ultrasound', 1),
(55, 'Ультразвуковая чистка (1 челюсть)', 'hygiene', 1600, 25, 'Частичная чистка', 'fa-ultrasound', 1),
(56, 'Отбеливание ZOOM (кабинетное)', 'hygiene', 14000, 90, 'Холодное светодиодное отбеливание', 'fa-sun', 1),
(57, 'Отбеливание Beyond (кабинетное)', 'hygiene', 13000, 80, 'Аналог ZOOM, более щадящее', 'fa-sun', 1),
(58, 'Домашнее отбеливание (капы + гель)', 'hygiene', 8000, 30, 'Индивидуальные капы + гель на курс', 'fa-moon', 1),
(59, 'Снятие налета пастой (без AirFlow)', 'hygiene', 1000, 20, 'Мягкая щетка и паста после снятия камня', 'fa-hand-sparkles', 1),
(60, 'Консультация ортодонта + ТРГ анализ', 'orthodontics', 1500, 40, 'Осмотр, диагностика, предварительный план', 'fa-chart-line', 1),
(61, 'Диагностика (слепки + расчет) под брекеты', 'orthodontics', 4000, 60, 'Снятие слепков, фото, рентген, расчет', 'fa-cube', 1),
(62, 'Брекет-система (металлическая, на 1 челюсть)', 'orthodontics', 35000, 90, 'Установка, лигатурные или самолигирующие', 'fa-braces', 1),
(63, 'Брекет-система (керамическая, на 1 челюсть)', 'orthodontics', 55000, 90, 'Эстетичные сапфировые/керамические', 'fa-gem', 1),
(64, 'Элайнеры (Invisalign/Star Smile) - курс', 'orthodontics', 120000, 60, 'Полный курс на 1 челюсть (легкая/средняя сложность)', 'fa-moon', 1),
(65, 'Ретенционный аппарат (капа) после брекетов', 'orthodontics', 8000, 40, 'Изготовление индивидуальной капы для фиксации', 'fa-hand-peace', 1),
(66, 'Закрытый кюретаж пародонтальных карманов', 'periodontology', 4000, 60, 'Чистка карманов до 4 мм на 1 сегмент', 'fa-hand-sparkles', 1),
(67, 'Открытый кюретаж (1 сегмент)', 'periodontology', 8500, 75, 'Лоскутная операция при пародонтите', 'fa-microscope', 1),
(68, 'Лечение пародонтита (Vector-терапия, 1 челюсть)', 'periodontology', 12000, 60, 'Ультразвуковая обработка карманов', 'fa-spray-can-sparkles', 1),
(69, 'Шинирование зубов (стекловолокно, 4 зуба)', 'periodontology', 7500, 60, 'Фиксация подвижных зубов при пародонтите', 'fa-link', 1),
(70, 'Лазерное лечение десен (1 сеанс)', 'periodontology', 3500, 30, 'Биостимуляция, антисептическая обработка', 'fa-sun', 1),
(71, 'Адаптационный прием ребенка', 'pediatric', 1000, 30, 'Знакомство, игра, первая консультация', 'fa-child', 1),
(72, 'Лечение кариеса молочного зуба (без бормашины, ICON)', 'pediatric', 3500, 30, 'Инфильтрационная терапия', 'fa-tooth', 1),
(73, 'Серебрение молочного зуба', 'pediatric', 1500, 15, 'Остановка кариеса фторидом серебра', 'fa-crown', 1),
(74, 'Герметизация фиссур (1 зуб)', 'pediatric', 2000, 25, 'Запечатывание жевательной поверхности', 'fa-hand-peace', 1),
(75, 'Удаление молочного зуба (простое)', 'pediatric', 2000, 15, 'Быстрое атравматичное удаление', 'fa-tooth', 1),
(76, 'Лечение пульпита молочного зуба (девитальная экстирпация)', 'pediatric', 4500, 60, 'Сохранение зуба до смены на постоянный', 'fa-microscope', 1),
(77, 'Удаление доброкачественной опухоли (фиброма, папиллома)', 'maxillofacial', 6000, 40, 'Лазерное или радиоволновое удаление', 'fa-cut', 1),
(78, 'Вправление вывиха височно-нижнечелюстного сустава', 'maxillofacial', 3500, 20, 'Одномоментное вправление', 'fa-bone', 1),
(79, 'Лечение невралгии тройничного нерва (блокада)', 'maxillofacial', 2500, 15, 'Медикаментозная блокада', 'fa-syringe', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(200) NOT NULL,
  `full_name` varchar(150) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `role` varchar(20) DEFAULT 'patient',
  `specialty` varchar(200) DEFAULT NULL,
  `experience` int(11) DEFAULT NULL,
  `bio` text,
  `schedule_json` text,
  `photo` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `certificates` text COMMENT 'JSON массив ссылок на сертификаты',
  `gender` enum('male','female') DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `agree_terms` tinyint(1) DEFAULT '0',
  `cabinet_number` varchar(20) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `user`
--

INSERT INTO `user` (`id`, `email`, `password_hash`, `full_name`, `phone`, `role`, `specialty`, `experience`, `bio`, `schedule_json`, `photo`, `is_active`, `created_at`, `certificates`, `gender`, `age`, `agree_terms`, `cabinet_number`) VALUES
(1, 'admin@dentalmaster.ru', '$2y$10$tJ7rB1Nrt1b66T6w2YlyEub7ckfUndxdM5qNxgvK1lpUBk/CVt15e', 'Администратор', '+7 (8412) 55-88-99', 'admin', NULL, NULL, NULL, NULL, NULL, 1, '2026-05-14 10:40:42', NULL, NULL, NULL, 0, '1'),
(2, 'vetrova@dentalmaster.ru', '$2y$10$gNJspsI32HHxY3MvOKsfOOkgOPDUMqdY18QADrHAi5ouOMea5.9L2', 'Анна Сергеевна Ветрова', '+7 (8412) 000-001', 'doctor', 'Имплантолог, стаж 14 лет', 14, '<i class=\"fas fa-graduation-cap\"></i> <strong>Образование:</strong> Московский государственный медико-стоматологический университет (МГМСУ), 2012 г.<br><i class=\"fas fa-certificate\"></i> <strong>Курсы повышения квалификации:</strong><br>• Имплантология с нуля до профи (2016, Швейцария)<br>• Костная пластика и синус-лифтинг (2018, Москва)<br>• Цифровое протоколирование имплантации (2021)<br><i class=\"fas fa-chart-line\"></i> <strong>Опыт:</strong> Более 3000 успешных имплантаций, автор статей в журнале \"Институт стоматологии\".<br><i class=\"fas fa-heart\"></i> <strong>Подход:</strong> Использую только проверенные системы имплантов (Straumann, Nobel Biocare).', '[\"09:00\",\"10:00\",\"11:00\",\"12:00\",\"14:00\",\"15:00\",\"16:00\"]', '1778769529.png', 1, '2026-05-14 10:40:42', '[\"1778770634_0_1.jpg\",\"1778770634_1_2.png\"]', NULL, NULL, 0, '1'),
(5, 'eliza.batkaeva7@yandex.ru', '$2y$10$K53Xltu0J7ZvIYrR.3wyJO33XnnBJS/CjcqKZWxjYOB.EX/BWvGu6', 'Баткаева Элиза Наилевна', '+79968072170', 'patient', NULL, NULL, NULL, NULL, NULL, 1, '2026-05-14 10:47:20', NULL, NULL, NULL, 0, '1'),
(6, 'eliza.batkaeva7@gmail.com', '$2y$10$UScbzu94jcibHqaEoc3Lh.UFq73iooLcOITtiYKObvGaG.pZTXigC', 'Баткаева Элиза Наилевна', '+79968072170', 'patient', NULL, NULL, NULL, NULL, NULL, 1, '2026-05-14 13:17:56', NULL, NULL, NULL, 0, '1'),
(7, 'ignatov@dentalmaster.ru', '$2y$10$SMLyCuzrOK6Y23T1mb.APOh96/Jlg/6GhMAlUC3se8p2SnZ2jWgXq', 'Игнатов Дмитрий Сергеевич', '+7 (8412) 000-011', 'doctor', 'Стоматолог-терапевт, стаж 11 лет', 11, '<i class=\"fas fa-graduation-cap\"></i> <strong>Образование:</strong> Кубанский государственный медицинский университет, 2013 г. Ординатура по терапевтической стоматологии, 2015 г.<br><i class=\"fas fa-certificate\"></i> <strong>Курсы:</strong><br>• Микроскопная эндодонтия (2017)<br>• Эстетическая реставрация фронтальной группы (2019)<br>• Лечение пульпита с использованием биокерамики (2021)<br><i class=\"fas fa-chart-line\"></i> <strong>Опыт:</strong> Более 5000 реставраций, восстанавливает зубы любой сложности под микроскопом.<br><i class=\"fas fa-smile\"></i> <strong>Подход:</strong> Работаю с системой CEREC для создания керамических вкладок за один визит.', '[\"09:00\",\"10:00\",\"11:00\",\"12:00\",\"14:00\",\"15:00\",\"16:00\"]', '1778768973.png', 1, '2026-05-14 14:17:51', '[\"1778770624_0_1.jpg\",\"1778770624_1_2.png\"]', NULL, NULL, 0, '2'),
(8, 'morozov@dentalmaster.ru', '$2y$10$SMLyCuzrOK6Y23T1mb.APOh96/Jlg/6GhMAlUC3se8p2SnZ2jWgXq', 'Морозов Евгений Павлович', '+7 (8412) 000-012', 'doctor', 'Стоматолог-хирург, стаж 15 лет', 15, '<i class=\"fas fa-graduation-cap\"></i> <strong>Образование:</strong> Первый МГМУ им. Сеченова, 2009 г. Хирургическая ординатура, 2011 г. Кандидат медицинских наук (2018).<br><i class=\"fas fa-certificate\"></i> <strong>Курсы:</strong><br>• Хирургическая имплантология в Швейцарии (2016)<br>• Атравматичное удаление зубов (2019)<br>• Микрохирургия в стоматологии (Израиль, 2022)<br><i class=\"fas fa-chart-line\"></i> <strong>Опыт:</strong> Более 8000 операций: удаление зубов любой сложности, резекция корня, цистэктомия.<br><i class=\"fas fa-shield-alt\"></i> <strong>Подход:</strong> Применяю современные методики обезболивания и минимально инвазивные технологии.', '[\"10:00\",\"11:00\",\"12:00\",\"13:00\",\"15:00\",\"16:00\",\"17:00\"]', '1778769043.png', 1, '2026-05-14 14:17:51', '[\"1778770640_0_1.jpg\",\"1778770640_1_2.png\"]', NULL, NULL, 0, '2'),
(9, 'ermakova@dentalmaster.ru', '$2y$10$SMLyCuzrOK6Y23T1mb.APOh96/Jlg/6GhMAlUC3se8p2SnZ2jWgXq', 'Ермаков Олег Алексеевич', '+7 (8412) 000-013', 'doctor', 'Стоматолог-ортопед, стаж 14 лет', 14, '<i class=\"fas fa-graduation-cap\"></i> <strong>Образование:</strong> СПбГМУ им. акад. И.П. Павлова, 2010 г. Ординатура по ортопедической стоматологии, 2012 г.<br><i class=\"fas fa-certificate\"></i> <strong>Курсы:</strong><br>• Виниры и люминиры (Вена, 2017)<br>• Бюгельное протезирование на имплантах (Мюнхен, 2019)<br>• CAD/CAM-технологии в ортопедии (Москва, 2023)<br><i class=\"fas fa-chart-line\"></i> <strong>Опыт:</strong> Более 2000 протезирований коронками E-max, цирконием, металлокерамикой.<br><i class=\"fas fa-microchip\"></i> <strong>Подход:</strong> Работаю с внутриротовым сканером, создаю протезы по 3D-модели без слепков.', '[\"09:30\",\"10:30\",\"11:30\",\"13:30\",\"14:30\",\"15:30\"]', '1778769441.png', 1, '2026-05-14 14:17:51', '[\"1778770661_0_1.jpg\",\"1778770661_1_2.png\"]', NULL, NULL, 0, '3'),
(10, 'nesterova@dentalmaster.ru', '$2y$10$SMLyCuzrOK6Y23T1mb.APOh96/Jlg/6GhMAlUC3se8p2SnZ2jWgXq', 'Нестерова Алина Константиновна', '+7 (8412) 000-014', 'doctor', 'Стоматолог-гигиенист, стаж 6 лет', 6, '<i class=\"fas fa-graduation-cap\"></i> <strong>Образование:</strong> Смоленский государственный медицинский университет, 2018 г.<br><i class=\"fas fa-certificate\"></i> <strong>Курсы:</strong><br>• AirFlow Master (2020)<br>• Отбеливание ZOOM 4 (2021)<br>• Индивидуальная программа профилактики кариеса (2023)<br><i class=\"fas fa-chart-line\"></i> <strong>Опыт:</strong> Более 4000 процедур профессиональной гигиены.<br><i class=\"fas fa-hand-sparkles\"></i> <strong>Подход:</strong> Деликатно работаю с эмалью, использую щадящие порошки AirFlow Plus.', '[\"09:00\",\"10:00\",\"11:00\",\"12:00\",\"14:00\",\"15:00\"]', '1778768903.png', 1, '2026-05-14 14:17:51', '[\"1778770668_0_1.jpg\",\"1778770668_1_2.png\"]', NULL, NULL, 0, '1'),
(11, 'subbotina@dentalmaster.ru', '$2y$10$SMLyCuzrOK6Y23T1mb.APOh96/Jlg/6GhMAlUC3se8p2SnZ2jWgXq', 'Субботина Ирина Валерьевна', '+7 (8412) 000-015', 'doctor', 'Стоматолог-ортодонт, стаж 10 лет', 10, '<i class=\"fas fa-graduation-cap\"></i> <strong>Образование:</strong> Российский университет дружбы народов, 2014 г. Ординатура по ортодонтии, 2016 г.<br><i class=\"fas fa-certificate\"></i> <strong>Курсы:</strong><br>• Брекет-системы Damon (Лондон, 2018)<br>• 3D-планирование ортодонтического лечения (Берлин, 2020)<br>• Элайнеры Invisalign (Москва, 2022)<br><i class=\"fas fa-chart-line\"></i> <strong>Опыт:</strong> Исправила прикус более 1200 пациентам, в том числе сложные клинические случаи.<br><i class=\"fas fa-child\"></i> <strong>Подход:</strong> Лечу детей и взрослых. Использую самолигирующие брекеты и прозрачные каппы.', '[\"10:00\",\"11:00\",\"12:00\",\"13:00\",\"14:00\",\"15:00\"]', '1778769605.png', 1, '2026-05-14 14:17:51', '[\"1778770679_0_1.jpg\",\"1778770679_1_2.png\"]', NULL, NULL, 0, '4'),
(18, 'vasilieva@dentalmaster.ru', '$2y$10$SMLyCuzrOK6Y23T1mb.APOh96/Jlg/6GhMAlUC3se8p2SnZ2jWgXq', 'Васильева Мария Игоревна', '+7 (8412) 000-017', 'doctor', 'Пародонтолог, стаж 7 лет', 7, '<i class=\"fas fa-graduation-cap\"></i> <strong>Образование:</strong> СамГМУ, 2019 г. Ординатура по терапевтической стоматологии, 2021 г. Специализация \"Пародонтология\", 2022 г.<br><i class=\"fas fa-certificate\"></i> <strong>Курсы:</strong><br>• Лазерное лечение заболеваний дёсен (2023)<br>• Вектор-терапия пародонтита (2024)<br><i class=\"fas fa-chart-line\"></i> <strong>Опыт:</strong> Лечение гингивита, пародонтита, кюретаж, шинирование подвижных зубов.<br><i class=\"fas fa-leaf\"></i> <strong>Подход:</strong> Предпочитаю биосовместимые материалы и минимальное вмешательство.', '[\"09:30\",\"10:30\",\"11:30\",\"13:30\",\"14:30\",\"16:00\"]', '1778769158.png', 1, '2026-05-14 14:18:36', '[\"1778770688_0_1.jpg\",\"1778770688_1_2.png\"]', NULL, NULL, 0, '5'),
(21, 'kuzmin@dentalmaster.ru', '$2y$10$SMLyCuzrOK6Y23T1mb.APOh96/Jlg/6GhMAlUC3se8p2SnZ2jWgXq', 'Кузьмин Роман Андреевич', '+7 (8412) 000-016', 'doctor', 'Врач-рентгенолог, стаж 8 лет', 8, '<i class=\"fas fa-graduation-cap\"></i> <strong>Образование:</strong> МГМСУ им. Евдокимова, 2018 г. Ординатура по лучевой диагностике, 2020 г.<br><i class=\"fas fa-certificate\"></i> <strong>Курсы:</strong><br>• КЛКТ в стоматологии (2021)<br>• 3D-планирование имплантации (2022)<br>• Внутриротовая сканирование и 3D-моделирование (2023)<br><i class=\"fas fa-chart-line\"></i> <strong>Опыт:</strong> Расшифровал более 5000 КЛКТ-снимков, помогаю хирургам и ортопедам планировать лечение.<br><i class=\"fas fa-eye\"></i> <strong>Подход:</strong> Анализирую не только зубы, но и ВНЧС, околоносовые пазухи, челюстные нервы.', '[\"09:00\",\"10:00\",\"11:00\",\"12:00\",\"14:00\",\"15:00\"]', '1778769254.png', 1, '2026-05-14 14:23:00', '[\"1778770695_0_1.jpg\",\"1778770695_1_2.png\"]', NULL, NULL, 0, '5'),
(22, 'batkaeva@gamil.com', '$2y$10$aALGI/n2XbnraTmynIUOr.SZmPqGnrfwYJrx1ceDt/zzNrPN4x9F2', 'Баткаева Элина Наилевна', '89968005003', 'patient', NULL, NULL, NULL, NULL, NULL, 1, '2026-05-17 14:28:37', NULL, 'female', 22, 1, '1'),
(23, 'etrova@dentalmaster.ru', '$2y$10$LrSAOhn.lKlwBfmfDIfxDOi6GYjOSUcd3ow5sfWOh78xv9ebHB9/S', 'Фитрова Анна Сергеевна', '+78412000001', 'patient', NULL, NULL, NULL, NULL, NULL, 1, '2026-05-17 15:04:13', NULL, 'female', 19, 1, '1'),
(24, '111@gmail.com', '$2y$10$FryHzBlGtAOcI1s/YmPb0O/u4dCxM2poiwkMjEIjzE3LXlAsOfT.a', 'Иванов Иван', '+79991234567', 'patient', NULL, NULL, NULL, NULL, NULL, 1, '2026-05-18 08:12:48', NULL, 'male', 25, 1, '1');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `appointment`
--
ALTER TABLE `appointment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `doctor_id` (`doctor_id`),
  ADD KEY `service_id` (`service_id`);

--
-- Индексы таблицы `review`
--
ALTER TABLE `review`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_rating` (`rating`),
  ADD KEY `idx_created` (`created_at`);

--
-- Индексы таблицы `service`
--
ALTER TABLE `service`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `appointment`
--
ALTER TABLE `appointment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT для таблицы `review`
--
ALTER TABLE `review`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT для таблицы `service`
--
ALTER TABLE `service`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

--
-- AUTO_INCREMENT для таблицы `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `appointment`
--
ALTER TABLE `appointment`
  ADD CONSTRAINT `appointment_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `appointment_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `appointment_ibfk_3` FOREIGN KEY (`service_id`) REFERENCES `service` (`id`);

--
-- Ограничения внешнего ключа таблицы `review`
--
ALTER TABLE `review`
  ADD CONSTRAINT `review_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
