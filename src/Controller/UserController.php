<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User as UserEntity;
use App\Form\Model\User as UserModel;
use App\Form\UserForm;
use App\Repository\UserRepository;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

final class UserController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private UserRepository $userRepository;
    private UserForm $userForm;
    private UserPasswordHasherInterface $passwordEncoder;
    private UserService $userService;

    public function __construct(
        EntityManagerInterface      $entityManager,
        UserRepository              $userRepository,
        UserForm                    $userForm,
        UserPasswordHasherInterface $passwordEncoder,
        UserService $userService
    ) {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->userForm = $userForm;
        $this->passwordEncoder = $passwordEncoder;
        $this->userService = $userService;
    }

    /**
     * @Route("/users", name="users")
     */
    public function list(): Response
    {
        $currentUser = $this->userService->getCurrentUser();
        if (!$currentUser->isAdmin()) {
            return $this->redirectToRoute('home');
        }

        $users = $this->userRepository->findAll();

        return $this->render('users/list.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/user/new", name="user_new")
     */
    public function new(Request $request): Response
    {
        $currentUser = $this->userService->getCurrentUser();
        if (!$currentUser->isAdmin()) {
            $this->redirect('home');
        }

        $entity = new UserEntity();
        $this->entityManager->persist($entity);

        $model = UserModel::createEmpty();
        $form = $this->userForm->generate($model);
        $success = $this->handleForm($form, $request, $model, $entity);

        if ($success) {
            return $this->redirectToRoute('users');
        }

        return $this->render('users/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/user/{id}/edit", name="user_edit")
     */
    public function edit(Request $request, int $id): Response
    {
        $currentUser = $this->userService->getCurrentUser();
        if (!$currentUser->isAdmin()) {
            $this->redirect('home');
        }

        $entity = $this->userRepository->find($id);
        if ($entity === null) {
            $this->addFlash('error', 'User not found');

            return $this->redirectToRoute('users');
        }

        $model = UserModel::createFromEntity($entity);
        $form = $this->userForm->generate($model);
        $success = $this->handleForm($form, $request, $model, $entity);

        if ($success) {
            return $this->redirectToRoute('users');
        }

        return $this->render('users/edit.html.twig', [
            'entity' => $entity,
            'model' => $model,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/user/{id}/delete", name="user_delete")
     */
    public function delete(int $id): Response
    {
        $user = $this->userRepository->find($id);
        if ($user === null) {
            $this->addFlash('error', 'User not found');

            return $this->redirectToRoute('users');
        }

        $notifications = $user->getNotifications();
        foreach ($notifications as $notification) {
            $user->removeNotification($notification);
            $this->entityManager->remove($notification);
        }
        $this->entityManager->remove($user);
        $this->entityManager->flush();

        $this->addFlash('success', 'User removed');

        return $this->redirectToRoute('users');
    }

    private function handleForm(FormInterface $form, Request $request, UserModel $model, UserEntity $entity): bool
    {
        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            return false;
        }

        if (!$form->isValid()) {
            $errors = $form->getErrors();

            foreach ($errors as $error) {
                assert($error instanceof FormError);
                $this->addFlash('error', $error->getMessage());
            }

            return false;
        }

        $entity->setUsername($model->username);
        $entity->setRoles($model->roles);

        if ($model->newPassword !== '') {
            $newPassword = $this->passwordEncoder->hashPassword($entity, $model->newPassword);
            $entity->setPassword($newPassword);
        }

        $this->entityManager->flush();
        $this->addFlash('success', 'User updated');

        return true;
    }
}