ALTER TABLE task MODIFY parent_id INT(11);
ALTER TABLE task MODIFY done TINYINT(1) default 0;
ALTER TABLE task MODIFY deleted TINYINT(1) default 0;
