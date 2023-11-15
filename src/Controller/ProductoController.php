<?php

namespace App\Controller;

use App\Entity\Producto;
use App\Service\GeneradorDeMensajes;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/producto', name: 'producto')]
class ProductoController extends AbstractController
{
  #[Route('', name: 'app_producto_create', methods: ['POST'])]
  public function create(EntityManagerInterface $entityManager, Request $request, GeneradorDeMensajes $generadorDeMensajes): JsonResponse
  {
    $producto = new Producto;
    if ($request->request->get('precio') <= 0) {
      return $this->json(['message' => 'error en los datos ingresados de precio, debe ser valores mayores a/o 0 no se puede registrar el producto']);
    } else if ($request->request->get('existencia') < 0) {
      return $this->json(['message' => 'error en los datos ingresados de existencias, debe ser valores mayores a 0, debe ser valores mayores a 0 no se puede registrar el producto']);
    }

    $producto->setNombre($request->request->get('nombre'));
    $producto->setPrecio($request->request->get('precio'));
    $producto->setExistencia($request->request->get('existencia'));

    $entityManager->persist($producto);

    $entityManager->flush();
    $random = rand(4, 5);
    return $this->json([
      'Message' => $generadorDeMensajes->getMensaje($random) . ' el producto' . $producto->getId()
    ]);
  }
  #[Route('', name: 'app_producto_read_all', methods: ['GET'])]
  public function readAll(EntityManagerInterface $entityManager, Request $request, GeneradorDeMensajes $generadorDeMensajes): JsonResponse
  {
    $repositorio = $entityManager->getRepository(Producto::class);
    $limit = $request->get('limit', 5);
    $page = $request->get('page', 1);
    $productos = $repositorio->findAllWithPagination($page, $limit);
    $total = $productos->count();
    $lastPage = (int) ceil($total / $limit);
    $data = [];

    foreach ($productos as $producto) {
      $data[] = [
        'id' => $producto->getId(),
        'nombre' => $producto->getNombre(),
        'precio' => $producto->getPrecio(),
        'existencia' => $producto->getExistencia()
      ];
    }
    $random = rand(0, 1);
    return $this->json([
      'Message' => $generadorDeMensajes->getMensaje($random) . ' los productos siguientes.',
      'data' => $data,
      'total' => $total,
      'lastPage' => $lastPage
    ]);
  }

  #[Route('/{id}', name: 'app_producto_read_one', methods: ['GET'])]
  public function readOne(EntityManagerInterface $entityManager, int $id, GeneradorDeMensajes $generadorDeMensajes): JsonResponse
  {
    $producto = $entityManager->getRepository(Producto::class)->find($id);

    if (!$producto) {
      return $this->json(['error' => 'No se encontro el producto.'], 404);
    }
    $random = rand(0, 1);
    return $this->json([
      'Message' => $generadorDeMensajes->getMensaje($random) . ' el producto.',
      'id' => $producto->getId(),
      'nombre' => $producto->getNombre(),
      'precio' => $producto->getPrecio(),
      'existencia' => $producto->getExistencia()
    ]);
  }

  #[Route('/{id}', name: 'app_producto_edit', methods: ['PUT'])]
  public function update(EntityManagerInterface $entityManager, int $id, Request $request, GeneradorDeMensajes $generadorDeMensajes): JsonResponse
  {
    // Busca el producto por id
    $producto = $entityManager->getRepository(Producto::class)->find($id);
    if ($request->request->get('precio') <= 0) {
      return $this->json(['message' => 'error en los datos ingresados de precio, debe ser valores mayores a/o 0 no se puede editar el producto']);
    } else if ($request->request->get('existencia') < 0) {
      return $this->json(['message' => 'error en los datos ingresados de existencias, debe ser valores mayores a 0 no se puede editar el producto']);
    }
    // Si no lo encuentra responde con un error 404
    if (!$producto) {
      return $this->json(['error' => 'No se encontro el producto con id: ' . $id], 404);
    }

    // Obtiene los valores del body de la request
    $nombre = $request->request->get('nombre');
    $precio = $request->request->get('precio');
    $existencia = $request->request->get('existencia');

    // Si no envia uno responde con un error 422
    if ($nombre == null || $precio == null || $existencia == null) {
      return $this->json(['error' => 'Se debe enviar el nombre, precio y existencia del producto.'], 422);
    }

    // Se actualizan los datos a la entidad
    $producto->setNombre($nombre);
    $producto->setPrecio($precio);
    $producto->setExistencia($existencia);

    $data = ['id' => $producto->getId(), 'nombre' => $producto->getNombre(), 'precio' => $producto->getPrecio(), 'existencia' => $producto->getExistencia()];

    // Se aplican los cambios de la entidad en la bd
    $entityManager->flush();
    $random = rand(6, 7);
    return $this->json([
      'Message' => $generadorDeMensajes->getMensaje($random) . ' el producto',
      'data' => $data
    ]);
  }

  #[Route('/{id}', name: 'app_producto_delete', methods: ['DELETE'])]
  public function delete(EntityManagerInterface $entityManager, int $id, Request $request, GeneradorDeMensajes $generadorDeMensajes): JsonResponse
  {

    // Busca el producto por id
    $producto = $entityManager->getRepository(Producto::class)->find($id);

    // Si no lo encuentra responde con un error 404
    if (!$producto) {
      return $this->json(['error' => 'No se encontro el producto con id: ' . $id], 404);
    }

    // Remueve la entidad
    $entityManager->remove($producto);

    $data = ['id' => $producto->getId(), 'nombre' => $producto->getNombre(), 'precio' => $producto->getPrecio(), 'existencia' => $producto->getExistencia()];

    // Se aplican los cambios de la entidad en la bd
    $entityManager->flush();
    $random = rand(3,4);
    return $this->json([
      'Message' => $generadorDeMensajes->getMensaje($random) .' el producto',
      'data' => $data
    ]);
  }
}
