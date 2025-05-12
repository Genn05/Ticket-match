<?php
namespace App\Controller\Admin;

use App\Entity\Mattch;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use Symfony\Component\Validator\Constraints as Assert;

class MattchCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Mattch::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('equipeA'),
            TextField::new('equipeB'),
            DateTimeField::new('dateMatch'),
            ImageField::new('imageEquipeDomicile')
                ->setBasePath('/uploads/images/')
                ->setUploadDir('public/uploads/images/')
                ->setFormTypeOptions([
                    'constraints' => [
                        new Assert\File([
                            'maxSize' => '1M', // 1MB file size limit
                            'mimeTypes' => ['image/jpeg', 'image/png', 'image/gif'],
                            'mimeTypesMessage' => 'Please upload a valid image (JPEG, PNG, or GIF).',
                        ])
                    ],
                ]),
            ImageField::new('imageEquipeExterieur')
                ->setBasePath('/uploads/images/')
                ->setUploadDir('public/uploads/images/')
                ->setFormTypeOptions([
                    'constraints' => [
                        new Assert\File([
                            'maxSize' => '1M', // 1MB file size limit
                            'mimeTypes' => ['image/jpeg', 'image/png', 'image/gif'],
                            'mimeTypesMessage' => 'Please upload a valid image (JPEG, PNG, or GIF).',
                        ])
                    ],
                ]),
        ];
    }
}
