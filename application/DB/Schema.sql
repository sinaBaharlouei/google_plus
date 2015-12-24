CREATE DATABASE google_plus;

CREATE TABLE google_user (

  id INT(11) NOT NULL auto_increment,
  username VARCHAR(255) NOT NULL,
  password VARCHAR (255) NOT NULL,
  user_type INT (1) NOT NULL DEFAULT 1,
  about VARCHAR(255),
  view INT(11) DEFAULT 0,
  place VARCHAR(255),
  created_at INT(11) NOT NULL,
  PRIMARY KEY(id),
  KEY(username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='list of users' AUTO_INCREMENT=0 ;

INSERT INTO google_user VALUES(1, 'admin', 'admin', 1, NULL, 0, 'Tehran' , 0);

-- ----------------------------------------------------------------------------------------------------

CREATE TABLE `user_info` (
  id INT(11) NOT NULL auto_increment,
  user_id INT(11) NOT NULL,
  category VARCHAR(255) NOT NULL,
  title VARCHAR (255) NOT NULL,
  value VARCHAR (255) NOT NULL DEFAULT 1,
  created_at INT(11) NOT NULL,
  PRIMARY KEY(id),
  KEY(user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='information of users' AUTO_INCREMENT=0 ;

-- ----------------------------------------------------------------------------------------------------

CREATE TABLE `user_follow` (
  follower_id INT(11) NOT NULL,
  followed_id INT(11) NOT NULL,
  PRIMARY KEY(follower_id, followed_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='follower tables' AUTO_INCREMENT=0 ;

-- --------------------------------------------------------------------------------------------------

CREATE TABLE `user_post` (
  id INT(11) NOT NULL auto_increment,
  user_id INT(11) NOT NULL,
  is_reported INT(1),
  content TEXT NOT NULL,
  created_at INT(11) NOT NULL,
  updated_at INT(11) NOT NULL,
  PRIMARY KEY(id),
  KEY(user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='information of users' AUTO_INCREMENT=0 ;
-- --------------------------------------------------------------------------------------------------

CREATE TABLE `comment_tag` (
  id INT(11) NOT NULL auto_increment,
  content TEXT NOT NULL,
  post_id INT(11) NOT NULL,
  type INT NOT NULL DEFAULT 1 COMMENT '1 for comment and 2 for tag',
  created_at INT(11) NOT NULL,
  PRIMARY KEY(id),
  KEY(post_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='information of comments and tag' AUTO_INCREMENT=0 ;

-- --------------------------------------------------------------------------------------------------

CREATE TABLE `post_like` (
  user_id INT(11) NOT NULL,
  post_id INT(11) NOT NULL,
  type  INT(1) DEFAULT 0 COMMENT '0 for like 1 for share',
  PRIMARY KEY(user_id, post_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='information of likes' AUTO_INCREMENT=0 ;


CREATE TABLE `post_share` (
  user_id INT(11) NOT NULL,
  post_id INT(11) NOT NULL,
  PRIMARY KEY(user_id, post_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='information of likes' AUTO_INCREMENT=0 ;

-- --------------------------------------------------------------------------------------------------

CREATE TABLE `user_comment` (
  id INT(11) NOT NULL auto_increment,
  user_id INT(11) NOT NULL ,
  post_id INT(11) NOT NULL ,
  content TEXT NOT NULL,
  created_at INT(11) NOT NULL,
  PRIMARY KEY(id),
  KEY(post_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='information of comments and tag' AUTO_INCREMENT=0 ;

-- ------------------------------------------------------------------------------------------------------
CREATE TABLE `user_reply` (
  id INT(11) NOT NULL auto_increment,
  user_id INT(11) NOT NULL ,
  comment_id INT(11) NOT NULL ,
  content TEXT NOT NULL,
  created_at INT(11) NOT NULL,
  PRIMARY KEY(id),
  KEY(user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='information of comments and tag' AUTO_INCREMENT=0 ;

-- ------------------------------------------------------------------------------------------------------------

CREATE TABLE `user_notification` (
  id INT(11) NOT NULL auto_increment,
  user_id INT(11) NOT NULL ,
  is_seen INT(1) NOT NULL DEFAULT 0,
  content TEXT NOT NULL,
  created_at INT(11) NOT NULL,
  PRIMARY KEY(id),
  KEY(user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='information of comments and tag' AUTO_INCREMENT=0 ;
