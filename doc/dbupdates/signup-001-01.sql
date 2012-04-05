ALTER TABLE signup ADD (is_before_deadline ENUM('Y','N'));
UPDATE signup SET is_before_deadline = 'Y';
COMMIT;
ALTER TABLE player DROP created_dts DROP updated_dts;
ALTER TABLE membership DROP created_dts DROP updated_dts;
ALTER TABLE scrimmage DROP created_dts DROP updated_dts;

