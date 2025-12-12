<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderFile;
use App\Form\OrderFileType;
use App\Repository\OrderFileRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/order/{orderId}/file')]
class OrderFileController extends AbstractController
{
    #[Route('/', name: 'app_order_file_index', methods: ['GET'])]
    public function index(int $orderId, OrderFileRepository $orderFileRepository): Response
    {
        $files = $orderFileRepository->findBy(['orderRelation' => $orderId]);

        return $this->render('order_file/index.html.twig', [
            'order_files' => $files,
            'order_id' => $orderId,
        ]);
    }

    #[Route('/new', name: 'app_order_file_new', methods: ['GET', 'POST'])]
    public function new(Request $request, int $orderId, EntityManagerInterface $entityManager): Response
    {
        $orderFile = new OrderFile();
        
        // Находим заказ
        $order = $entityManager->getRepository(Order::class)->find($orderId);
        if (!$order) {
            throw $this->createNotFoundException('Заказ не найден');
        }
        
        $orderFile->setOrderRelation($order);
        
        $form = $this->createForm(OrderFileType::class, $orderFile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($orderFile);
            $entityManager->flush();

            return $this->redirectToRoute('app_order_file_index', [
                'orderId' => $orderId
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->render('order_file/new.html.twig', [
            'order_file' => $orderFile,
            'form' => $form,
            'order_id' => $orderId,
        ]);
    }

    #[Route('/{id}', name: 'app_order_file_show', methods: ['GET'])]
    public function show(OrderFile $orderFile): Response
    {
        return $this->render('order_file/show.html.twig', [
            'order_file' => $orderFile,
        ]);
    }

    #[Route('/{id}/download', name: 'app_order_file_download', methods: ['GET'])]
    public function download(OrderFile $orderFile): Response
    {
        $filePath = $this->getParameter('kernel.project_dir') . '/public' . $orderFile->getFileUrl();
        
        if (!file_exists($filePath)) {
            throw $this->createNotFoundException('Файл не найден');
        }

        $response = new StreamedResponse(function () use ($filePath) {
            $handle = fopen($filePath, 'rb');
            while (!feof($handle)) {
                $buffer = fread($handle, 1024);
                echo $buffer;
                flush();
            }
            fclose($handle);
        });

        $response->headers->set('Content-Type', $orderFile->getMimeType());
        $response->headers->set('Content-Disposition', 
            HeaderUtils::makeDisposition(
                HeaderUtils::DISPOSITION_ATTACHMENT,
                $orderFile->getOriginalName()
            )
        );

        return $response;
    }

    #[Route('/{id}', name: 'app_order_file_delete', methods: ['POST'])]
    public function delete(Request $request, OrderFile $orderFile, EntityManagerInterface $entityManager): Response
    {
        $orderId = $orderFile->getOrderRelation()->getId();
        
        if ($this->isCsrfTokenValid('delete'.$orderFile->getId(), $request->request->get('_token'))) {
            // Удаляем физический файл
            $filePath = $this->getParameter('kernel.project_dir') . '/public' . $orderFile->getFileUrl();
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            
            // Удаляем запись из БД
            $entityManager->remove($orderFile);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_order_file_index', [
            'orderId' => $orderId
        ], Response::HTTP_SEE_OTHER);
    }
}