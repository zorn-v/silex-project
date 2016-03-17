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

## Requirements
By default you need a ```users``` table with ```login``` and ```password``` fields
