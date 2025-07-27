<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("ROLE_ADMIN")]
class UserCrudController extends AbstractCrudController {

    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly EntityManagerInterface      $entityManager
    ) {
    }

    public static function getEntityFqcn(): string {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud {
        return Crud::new()
            ->setEntityLabelInSingular('Gebruiker')
            ->setEntityLabelInPlural('Gebruikers')
            ->setPaginatorPageSize(50)
            ->renderContentMaximized()
            ->showEntityActionsInlined();
    }

    public function configureActions(Actions $actions): Actions {
        return $actions
            ->remove(Crud::PAGE_INDEX, Action::BATCH_DELETE);
    }

    public function configureFields(string $pageName): iterable {
        $email = EmailField::new('email')
            ->setLabel('E-mailadres');

        $password = TextField::new('plainPassword')
            ->setFormType(PasswordType::class)
            ->onlyOnForms()
            ->setLabel('Wachtwoord');
        if ($pageName === Crud::PAGE_NEW) {
            $password->setRequired(true);
        } elseif ($pageName === Crud::PAGE_EDIT) {
            $password->setRequired(false);
            $password->setHelp('Laat leeg om het wachtwoord niet te wijzigen.');
        }

        yield FormField::addFieldset('Gebruikergegevens');
        yield $email;
        yield $password;
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void {
        if (!$entityInstance->getEmail() || !$entityInstance->getPlainPassword()) return;
        if ($entityInstance->getPlainPassword()) {
            $entityInstance->setPassword($this->passwordHasher->hashPassword($entityInstance, $entityInstance->getPlainPassword()));
        }

        $entityManager->persist($entityInstance);
        $entityManager->flush();
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void {
        /* @var User $entityInstance */
        if ($entityInstance->getPlainPassword()) {
            $entityInstance->setPassword($this->passwordHasher->hashPassword($entityInstance, $entityInstance->getPlainPassword()));
        }

        $entityManager->persist($entityInstance);
        $entityManager->flush();
    }
}
