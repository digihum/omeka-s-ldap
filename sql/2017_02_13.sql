#! UP
CREATE TABLE `module_ldap_users` (
  `id` INT NOT NULL,
  `username` VARCHAR(255) NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `id`
    FOREIGN KEY (`id`)
    REFERENCES `user` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE);


#----------------#


#! DOWN
DROP TABLE `module_ldap_users`;