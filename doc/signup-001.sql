DROP TABLE IF EXISTS player;
CREATE TABLE player
( id            int(11)       NOT NULL  AUTO_INCREMENT
, name          varchar(255)  NOT NULL
, email         varchar(255)
, membership_id int(11)       NOT NULL DEFAULT '1'
, is_goalie     ENUM('Y','N') NOT NULL DEFAULT 'N'
, password      varchar(255)
, created_dts   varchar(255)  NOT NULL
, updated_dts   varchar(255)  NOT NULL
, PRIMARY KEY (id)
);

DROP TABLE IF EXISTS membership;
CREATE TABLE membership
( id            int(11)       NOT NULL AUTO_INCREMENT
, type          int(11)       NOT NULL DEFAULT 1
, amount_paid   decimal(3,2)
, created_dts   varchar(255)  NOT NULL
, updated_dts   varchar(255)  NOT NULL
, PRIMARY KEY (id)
);

DROP TABLE IF EXISTS scrimmage;
CREATE TABLE scrimmage
( id            int(11)       NOT NULL AUTO_INCREMENT
, date          date          NOT NULL
, start_time    varchar2(5)   NOT NULL
, location      varchar(255)  NOT NULL
, current_start datetime      NOT NULL
, current_end   datetime      NOT NULL
, created_dts   varchar(255)  NOT NULL
, updated_dts   varchar(255)  NOT NULL
, PRIMARY KEY (id)
);

DROP TABLE IF EXISTS signup;
CREATE TABLE signup
( id            int(11)       NOT NULL AUTO_INCREMENT
, player_id     int(11)       NOT NULL
, scrimmage_id  int(11)       NOT NULL
, priority      int(11)       NOT NULL DEFAULT 0
, position      varchar(255)  NOT NULL DEFAULT 'skater'
, signup_dts    varchar(255)  NOT NULL
, PRIMARY KEY (id)
);

