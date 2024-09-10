<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\BookRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;


class BookController extends AbstractController
{
    #[Route('/books', name: 'get_books', methods: ['GET'])]
    public function index(BookRepository $bookRepository): JsonResponse
    {
        return $this->json([
            'books' => $bookRepository->findAll(),
        ]);
    }

    #[Route('/books/{id}', name: 'get_bookByID', methods: ['GET'])]
    public function show(int $id, BookRepository $bookRepository): JsonResponse
    {
        $id = $bookRepository->find($id);
        if(!$id) throw $this->createNotFoundException('The book does not exist');

        return $this->json([
            'data' => $id,
        ]);
    }

    #[Route('/books', name: 'create_book', methods: ['POST'])]
    public function create(Request $request, BookRepository $bookRepository): JsonResponse
    {
        if ($request->headers->get('Content-Type') == 'application/json') {
            $data = $request->toArray();
        } else {
            $data = $request->request->all();
        }

        $book = new Book();
        $book->setTitle($data['title']);
        $book->setIsbn($data['isbn']);
        $book->setCreatedAt(new \DateTimeImmutable('now', new \DateTimeZone('Europe/Lisbon')));
        $book->setUpdatedAt(new \DateTimeImmutable('now', new \DateTimeZone('Europe/Lisbon')));

        $bookRepository->add($book, true);

        return $this->json([
            'message' => 'Book created successfully!',
            'path' => 'src/Controller/BookController.php',
        ], 201);
    }

    #[Route('/books/{id}', name: 'update_book', methods: ['PUT' , 'PATCH'])]
    public function update(int $id, Request $request, ManagerRegistry $managerRegistry,
                           BookRepository $bookRepository): JsonResponse
    {
        if ($request->headers->get('Content-Type') == 'application/json') {
            $data = $request->toArray();
        } else {
            $data = $request->request->all();
        }

        $book = $bookRepository->find($id);
        if(!$book) throw $this->createNotFoundException('The book does not exist');

        $book->setTitle($data['title']);
        $book->setIsbn($data['isbn']);
        $book->setUpdatedAt(new \DateTimeImmutable('now', new \DateTimeZone('Europe/Lisbon')));

        $managerRegistry->getManager()->flush();

        return $this->json([
            'message' => 'Book updated successfully!',
            'data' => $book,
            'path' => 'src/Controller/BookController.php',
        ]);
    }

    #[Route('/books/{id}', name: 'delete_book', methods: ['DELETE'])]
    public function delete(int $id, BookRepository $bookRepository): JsonResponse
    {
        $book = $bookRepository->find($id);
        if(!$book) throw $this->createNotFoundException('The book does not exist');

        $bookRepository->remove($book, true);

        return $this->json([
            'message' => 'Book deleted successfully!',
            'path' => 'src/Controller/BookController.php',
        ]);
    }


}
