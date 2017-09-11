CREATE TABLE `users_xingzuo_data` (
  `wxid` varchar(255) default null,
  `birth_address` varchar(512) default null,
  `live_address` varchar(512) default null,
  `name` varchar(60) NOT NULL,
  `sex` varchar(255) DEFAULT NULL,
  `birthDay` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE `users_xingpan_data` (
  `wxid` varchar(255) default null,
  `result` text default null
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



CREATE TABLE `users_zhanxing_history` (
  `wxid` varchar(255) default null,
  `question_name` varchar(255) default null,
  `question_answer` varchar(255) default null,
  `zx_date` Date default null,
  `result` text default null
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

