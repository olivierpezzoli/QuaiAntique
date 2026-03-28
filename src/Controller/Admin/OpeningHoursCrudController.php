<?php

namespace App\Controller\Admin;

use App\Entity\OpeningHours;
use App\Entity\Weekday;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TimeField;
use Symfony\Component\Form\Extension\Core\Type\EnumType;

class OpeningHoursCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return OpeningHours::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural('Horraires')
            ->setEntityLabelInSingular('Horraire');
    }

    public function configureFields(string $pageName): iterable
    {
        $weekday = ChoiceField::new('day', 'Name')
            ->setFormType(EnumType::class)
            ->setFormTypeOption('class', Weekday::class)
            ->setChoices([
                "day" => Weekday::cases(),
            ]);

        if (Crud::PAGE_INDEX === $pageName || Crud::PAGE_DETAIL === $pageName) {
            $weekday->setChoices(Weekday::getAsArray());
        }

        return [
            $weekday,
            BooleanField::new('isDayClosed', 'Jour de fermeture'),
            BooleanField::new('isLunchClosed', 'Fermé le midi'),
            TimeField::new('lunchStart', 'Début midi'),
            TimeField::new('lunchEnd', 'Fin midi'),
            IntegerField::new('lunchMaxPlaces', 'Nb places midi'),
            BooleanField::new('isEveningClosed', 'Fermé le soir'),
            TimeField::new('eveningStart', 'Début soir'),
            TimeField::new('eveningEnd', 'Fin soir'),
            IntegerField::new('eveningMaxPlaces', 'Nb places soir'),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ->remove(Crud::PAGE_INDEX, Action::DELETE);
    }
}
