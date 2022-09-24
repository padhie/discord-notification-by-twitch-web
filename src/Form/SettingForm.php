<?php

declare(strict_types=1);

namespace App\Form;

use App\Form\Model\Setting;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormInterface;

final class SettingForm
{
    private FormFactory $formFactory;

    public function __construct(FormFactory $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    public function generate(Setting $setting): FormInterface
    {
        $formBuilder = $this->formFactory->createBuilder(FormType::class, $setting);

        $this->generateFields($formBuilder);
        $formBuilder->add('save', SubmitType::class, ['label' => 'Save']);

        return $formBuilder->getForm();
    }

    private function generateFields(FormBuilderInterface $formBuilder): void
    {
        $formBuilder->add(
            'notificationActive',
            CheckboxType::class,
            [
                'required' => false,
                'label' => 'notification active',
            ]
        );

        $formBuilder->add(
            'notificationInactiveUntil',
            TextType::class,
            [
                'required' => false,
                'label' => 'notification active until',
            ]
        );
    }
}
