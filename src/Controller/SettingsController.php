<?php

namespace App\Controller;

use App\Entity\Setting;
use App\Form\Model\Setting as SettingModel;
use App\Form\SettingForm;
use App\Repository\SettingRepository;
use App\Repository\StateRepository;
use App\Service\LastStateService;
use App\Service\UserService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class SettingsController extends AbstractController
{
    public function __construct(
        private readonly LastStateService $lastStateService,
        private readonly UserService $userService,
        private readonly StateRepository $stateRepository,
        private readonly SettingRepository $settingRepository,
        private readonly SettingForm $settingForm,
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    /**
     * @Route("/settings", name="settings")
     */
    public function index(Request $request): Response
    {
        $currentUser = $this->userService->getCurrentUser();
        if (!$currentUser->isAdmin()) {
            return $this->redirectToRoute('home');
        }

        $fileStates = $this->lastStateService->getState();
        $dbStates = $this->getDatabaseState();

        $settingEntities = $this->settingRepository->findAll();
        $settingModel = SettingModel::createFromEntities($settingEntities);
        $form = $this->settingForm->generate($settingModel);

        $this->handleForm($form, $request, $settingModel, $settingEntities);

        return $this->render('settings/index.html.twig', [
            'lastFileState' => $fileStates,
            'lastDbState' => $dbStates,
            'settingForm' => $form->createView(),
        ]);
    }

    /**
     * @return array<int, array<string, string|int>>
     */
    private function getDatabaseState(): array
    {
        $states = $this->stateRepository->findAll();
        $data = [];

        foreach ($states as $state) {
            $data[] = [
                'name' => $state->getChannel(),
                'online' => $state->isOnline() ? 1 : 0,
            ];
        }

        return $data;
    }

    /**
     * @param Setting[] $settingEntities
     */
    private function handleForm(FormInterface $form, Request $request, SettingModel $settingModel, array $settingEntities): FormInterface
    {
        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            return $form;
        }

        if (!$form->isValid()) {
            $errors = $form->getErrors();

            foreach ($errors as $error) {
                assert($error instanceof FormError);
                $this->addFlash('error', $error->getMessage());
            }

            return $form;
        }

        foreach ($settingEntities as $settingEntity) {
            $key = $settingEntity->getKey();
            $newValue = $settingModel->{$key};

            if ($newValue === null) {
                continue;
            }

            if (is_bool($newValue)) {
                $newValue = $newValue
                    ? '1'
                    : '0';
            }

            if ($newValue instanceof DateTime) {
                $newValue = $newValue->format('Y-m-d H:i:s');
            }

            $settingEntity->setValue($newValue);
        }

        $this->entityManager->flush();
        $this->addFlash('success', 'Successful saved');

        return $form;
    }
}
