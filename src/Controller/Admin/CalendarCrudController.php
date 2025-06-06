<?php

namespace App\Controller\Admin;

use App\Entity\Calendar;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class CalendarCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Calendar::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
        ->setEntityLabelInSingular('Calendrier')
        ->setEntityLabelInPlural('Calendrier')
        ->setDateFormat('dd/MM/yyyy')
        ->setTimeFormat('HH:mm')
        ->setSearchFields(['day']);
    //         ->setPageTitle(Crud::PAGE_INDEX, 'Calendar Management')
    //         ->setPageTitle(Crud::PAGE_DETAIL, 'Calendar Details')
    //         ->setPageTitle(Crud::PAGE_NEW, 'Create New Calendar Event')
    //         ->setPageTitle(Crud::PAGE_EDIT, 'Edit Calendar Event')
    //         ->setDefaultSort(['date' => 'DESC'])
    //         ->setPaginatorPageSize(20)
    //         ->setPaginatorRangeSize(5);
    }
    // public function configureActions(Actions $actions): Actions
    // {
    //     // You can customize the CRUD configuration here if needed
    //     // For example, you can set the page title, enable/disable actions, etc.
    // }
    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
