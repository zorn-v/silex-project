# Silex project skeleton

Silex is PHP microframework based on symfony components - http://silex.sensiolabs.org/
By default you have only bare silex and you need to organizing architecture on your own.

With this repo you can do ```composer create-project zorn-v/silex-project``` and you have:
- Eloquient ORM model ```User``` that also implements ```Symfony\Component\Security\Core\User\UserInterface``` and used by ```UserProvider```
- Twig template engine with symfony/twig-bridge (path function etc.)
- ```asset``` function for twig
- Sample routes and contollers for index and login
- Symfony form component (without sample yet)
- Symfony form layout without dependency from symfony/translation component
- OAuth authentication with examples

## Requirements
By default you need a folowing DB schema
```
CREATE TABLE `oauth_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `service` varchar(255) NOT NULL,
  `uid` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `service` (`service`,`uid`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB;

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;


ALTER TABLE `oauth_users`
  ADD CONSTRAINT `oauth_users_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
```
