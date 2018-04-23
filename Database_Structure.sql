SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `autologin` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `hash` varchar(255) NOT NULL,
  `validator` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `members` (
  `id` int(11) NOT NULL,
  `email` varchar(30) NOT NULL,
  `password` varchar(255) NOT NULL,
  `firstname` varchar(20) NOT NULL,
  `lastname` varchar(20) NOT NULL,
  `joindate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `resetpass` (
  `user_id` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `expire` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `autologin`
  ADD UNIQUE KEY `id_2` (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD KEY `id` (`id`);

ALTER TABLE `members`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `resetpass`
  ADD UNIQUE KEY `user_id_2` (`user_id`),
  ADD KEY `user_id` (`user_id`);


ALTER TABLE `autologin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;