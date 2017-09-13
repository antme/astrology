CREATE TABLE `users_xingzuo_data` (
  `wxid` varchar(255) default null,
  `birth_address` varchar(512) default null,
  `live_address` varchar(512) default null,
  `name` varchar(60) NOT NULL,
  `sex` varchar(255) DEFAULT NULL,
  `birthDay` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE `users_wei_xin` (
  `openid` varchar(255) default null,
  `nickname` varchar(255) default null,
  `sex` varchar(255) default null,
  `province` varchar(255) default null,
  `city` varchar(255) default null,
  `country` varchar(255) default null,
  `headimgurl` varchar(512) default null
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



CREATE TABLE `wx_ticket_token` (
  `token` varchar(512) default null,
  `type` varchar(255) default null,
  `expire_time` int default 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



CREATE TABLE `users_login` (
  `openid` varchar(255) default null,
  `ast_c_id_session_id` varchar(255) default null,
  `expire_time` int default 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


