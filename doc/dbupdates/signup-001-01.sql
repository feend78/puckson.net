ALTER TABLE signup ADD (is_before_deadline ENUM('Y','N'));
UPDATE signup SET is_before_deadline = 'Y';
COMMIT;
ALTER TABLE player DROP COLUMN created_dts;
ALTER TABLE player DROP COLUMN updated_dts;
ALTER TABLE membership DROP COLUMN created_dts;
ALTER TABLE membership DROP COLUMN updated_dts;
ALTER TABLE signup DROP COLUMN created_dts;
ALTER TABLE signup DROP COLUMN updated_dts;
ALTER TABLE scrimmage DROP COLUMN created_dts;
ALTER TABLE scrimmage DROP COLUMN updated_dts;

