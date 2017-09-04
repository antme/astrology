CREATE TABLE `users_xingzuo_data` (
  `wxid` varchar(255) default null,
  `birth_address` varchar(512) default null,
  `live_address` varchar(512) default null,
  `name` varchar(60) NOT NULL,
  `sex` varchar(255) DEFAULT NULL,
  `birthDay` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;