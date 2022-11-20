<?php

declare(strict_types=1);

namespace App\Form;

use App\Form\Model\User;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormInterface;

final class UserForm
{
    private FormFactory $formFactory;

    public function __construct(FormFactory $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    public function generate(User $user): FormInterface
    {
        $formBuilder = $this->formFactory->createBuilder(FormType::class, $user);

        $this->generateFields($formBuilder);
        $formBuilder->add(
            'submit',
            SubmitType::class,
            [
                'label' => 'Save',
                'attr' => [
                    'class' => 'btn btn-success',
                ]
            ]
        );

        return $formBuilder->getForm();
    }

    private function generateFields(FormBuilderInterface $formBuilder): void
    {
        $formBuilder->add(
            'username',
            TextType::class,
            [
                'required' => true,
                'label' => 'Username',
                'attr' => [
                    'class' => 'form-control',
                ],
            ]
        );

        $formBuilder->add(
            'newPassword',
                PasswordType::class,
            [
                'required' => false,
                'label' => 'new Password',
                'attr' => [
                    'class' => 'form-control',
                ],
            ]
        );

        $formBuilder->add(
            'roles',
            ChoiceType::class,
            [
                'required' => false,
                'label' => 'Roles',
                'multiple' => true,
                'choices' => [
                    'ROLE_ADMIN' => 'ROLE_ADMIN',
                    'ROLE_USER' => 'ROLE_USER',
                    'ANONYMOUS' => 'ANONYMOUS',
                ],
                'attr' => [
                    'class' => 'form-control',
                ],
            ]
        );
    }
}
