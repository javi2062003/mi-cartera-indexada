<?php

namespace App\Command;

use App\Entity\Portfolio;
use App\Entity\Transaction;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-demo-portfolio',
    description: 'Crea un usuario demo con un portfolio y transacciones.',
)]
class AppCreateDemoPortfolioCommand extends Command
{

    public function __construct(
        private EntityManagerInterface $entityManager, // Para hablar con la base de datos
        private UserPasswordHasherInterface $passwordHasher // Para codificar la contraseña del usuario
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Creando una demo del Portfolio');

 
        $io->section('1. Crear usuario');
        $user = new User();
        $user->setEmail('test@user.com');
        $user->setFullName('Usuario Prueba');
        
        $hashedPassword = $this->passwordHasher->hashPassword($user, 'password123');
        $user->setPassword($hashedPassword);
        $io->writeln('Se ha creado el usuario: test@user.com');

        $io->section('2. Creando Portfolio');
        $portfolio = new Portfolio();
        $portfolio->setName('Mi Cartera Global Indexada');
        $portfolio->setOwner($user); 
        $io->writeln('Portfolio creado y relacionado con User.');

        $io->section('3.Crear transacciones');
        
        $transaction1 = new Transaction();
        $transaction1->setType('BUY');
        $transaction1->setTicker('VWCE.DE');
        $transaction1->setAssetName('Vanguard FTSE All-World UCITS ETF');
        $transaction1->setShares(10);
        $transaction1->setPrice(105.50);
        $transaction1->setDate(new \DateTime('-1 month'));
        $transaction1->setPortfolio($portfolio); 
        $io->writeln('Transaccion 1 (VWCE.DE) creada y relacionada con portfolio.');

        $transaction2 = new Transaction();
        $transaction2->setType('BUY');
        $transaction2->setTicker('AGGH.AS');
        $transaction2->setAssetName('iShares Global Aggregate Bond UCITS ETF');
        $transaction2->setShares(25);
        $transaction2->setPrice(5.10);
        $transaction2->setDate(new \DateTime('-15 days'));
        $transaction2->setPortfolio($portfolio); 
        $io->writeln('Transaccion 2 (AGGH.AS)creada y relacionada con portfolio.');

        $io->section('4. Persistir objetos en Doctrine');
        $this->entityManager->persist($user);
        $this->entityManager->persist($portfolio);
        $this->entityManager->persist($transaction1);
        $this->entityManager->persist($transaction2);
        $io->writeln('Todos los objetos son controlados por Doctrine.');

 
        $io->section('5. Cambios en la bbdd');
        $this->entityManager->flush();
        $io->writeln('La bbdd ha sido actualizada.');

        $io->success('La demo del portfolio ha sido creada, mira tu bbdd.');

        return Command::SUCCESS;
    }
}