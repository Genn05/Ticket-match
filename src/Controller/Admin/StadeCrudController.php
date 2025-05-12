<?php

namespace App\Controller\Admin;

use App\Entity\Stade;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;

class StadeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Stade::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('nom'),
            TextField::new('ville'),
            IntegerField::new('capacite'),
        ];
    }
}
