<?php

namespace App\DataFixtures;

use App\Entity\Usuario;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager)
    {
        // Crear un usuario admin con ROLE_ADMIN
        $adminUser = new Usuario();
        $adminUser->setUsername('admin');
        $adminUser->setRoles(['ROLE_ADMIN']);
        $adminUser->setFuncion("ADMIN");
        $adminUser->setNombreUsuario("admin");
        $fechaActual = new DateTime();
        $adminUser->setFechaCreacionUsuario($fechaActual);
        // Hashear la contraseña y asignarla al usuario
        $hashedPassword = $this->passwordHasher->hashPassword($adminUser, 'admin');
        $adminUser->setPassword($hashedPassword);

        // Persistir el usuario en la base de datos
        $manager->persist($adminUser);
        $manager->flush();
    }
}
