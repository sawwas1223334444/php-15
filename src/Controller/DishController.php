<?php

namespace App\Controller;

use App\Entity\Dish;
use App\Form\DishType;
use App\Repository\DishRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/dish')]
final class DishController extends AbstractController
{
    #[Route(name: 'app_dish_index', methods: ['GET'])]
    public function index(DishRepository $dishRepository): Response
    {
        return $this->render('dish/index.html.twig', [
            'dishes' => $dishRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_dish_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $dish = new Dish();
        $form = $this->createForm(DishType::class, $dish);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($dish);
            $entityManager->flush();

            return $this->redirectToRoute('app_dish_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('dish/new.html.twig', [
            'dish' => $dish,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_dish_show', methods: ['GET'])]
    public function show(Dish $dish): Response
    {
        return $this->render('dish/show.html.twig', [
            'dish' => $dish,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_dish_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Dish $dish, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DishType::class, $dish);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_dish_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('dish/edit.html.twig', [
            'dish' => $dish,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_dish_delete', methods: ['POST'])]
    public function delete(Request $request, Dish $dish, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$dish->getId(), $request->getPayload()->getString('_token'))) {
            // Удаляем файл изображения, если он существует
            if ($dish->getImageName()) {
                $filePath = $this->getParameter('kernel.project_dir') . '/public/images/dishes/' . $dish->getImageName();
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
            
            $entityManager->remove($dish);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_dish_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/delete-image', name: 'app_dish_delete_image', methods: ['POST'])]
    public function deleteImage(Request $request, Dish $dish, EntityManagerInterface $entityManager): Response
    {
        // Используем getPayload() для Symfony 6.3+
        if ($this->isCsrfTokenValid('delete-image'.$dish->getId(), $request->getPayload()->getString('_token'))) {
            // Удаляем физический файл
            if ($dish->getImageName()) {
                $filePath = $this->getParameter('kernel.project_dir') . '/public/images/dishes/' . $dish->getImageName();
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
            
            // Очищаем поля в базе данных
            $dish->setImageFile(null);
            $dish->setImageName(null);
            $dish->setImageSize(null);
            $dish->setUpdatedAt(new \DateTimeImmutable());
            
            $entityManager->flush();
            
            $this->addFlash('success', 'Фото успешно удалено.');
        } else {
            $this->addFlash('error', 'Неверный токен безопасности.');
        }

        return $this->redirectToRoute('app_dish_edit', ['id' => $dish->getId()]);
    }
}