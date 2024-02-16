<?php
namespace App\DataFixtures;

use App\Entity\BriefingWeb;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class DatosPruebaBriefingWebFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // Crear instancia de Faker
        $faker = Factory::create();

        // Crear y persistir varios objetos BriefingWeb de prueba
        for ($i = 0; $i < 10; $i++) {
            $briefingWeb = new BriefingWeb();
            $briefingWeb->setDescripcionEmpresa($faker->sentence);
            $briefingWeb->setDescripcionProyecto($faker->paragraph);
            $briefingWeb->setProductosYOServicios($faker->words(3, true));
            $briefingWeb->setCompetencia($faker->sentence);
            $briefingWeb->setObjetivos($faker->paragraph);
            $briefingWeb->setWebEjemplo([$faker->url]);
            $briefingWeb->setEstructuraYContenido($faker->paragraph);
            $briefingWeb->setFunciones($faker->paragraph);
            $briefingWeb->setDisenyoImagen([$faker->imageUrl()]);
            $briefingWeb->setMantenimiento($faker->boolean);
            $briefingWeb->setDominio($faker->domainName);
            $briefingWeb->setComentarios($faker->paragraph);
            $briefingWeb->setFechaCreacionBriefingWeb($faker->dateTimeThisYear);

            $manager->persist($briefingWeb);
        }

        // Guardar los cambios en la base de datos
        $manager->flush();
    }
}
