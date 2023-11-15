<?php

namespace App\Controller;

use App\Entity\Direccion;
use App\Service\GeneradorDeMensajes;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/direccion', name: 'direccion')]
class DireccionController extends AbstractController
{
  #[Route('', name: 'app_direccion_create', methods: ['POST'])]
  public function create(EntityManagerInterface $entityManager, Request $request, GeneradorDeMensajes $generadorDeMensajes): JsonResponse
  {
    $direccion = new Direccion();
    $direccion->setUsuario($request->request->get('usuario_id'));
    $direccion->setDepartamento($request->request->get('departamento'));
    $direccion->setMunicipio($request->request->get('municipio'));
    $direccion->setDireccion($request->request->get('direccion'));

    $entityManager->persist($direccion);

    $entityManager->flush();
    $random = rand(4, 5);
    return $this->json([
      'Message' => $generadorDeMensajes->getMensaje($random) . ' la direccion.' . $direccion->getId()
    ]);
  }

  #[Route('', name: 'app_direccion_read_all', methods: ['GET'])]
  public function readAll(EntityManagerInterface $entityManager, Request $request, GeneradorDeMensajes $generadorDeMensajes): JsonResponse
  {
    $repositorio = $entityManager->getRepository(Direccion::class);
    $limit = $request->get('limit', 5);
    $page = $request->get('page', 1);
    $direcciones = $repositorio->findAllWithPagination($page, $limit);
    $total = $direcciones->count();
    $lastPage = (int) ceil($total / $limit);

    $data = [];

    foreach ($direcciones as $direccion) {
      $data[] = [
        'id' => $direccion->getId(),
        'usuario_id' => $direccion->getUsuario()->getId(),
        'departamento' => $direccion->getDepartamento(),
        'municipio' => $direccion->getMunicipio(),
        'direccion' => $direccion->getDireccion()
      ];
    }
    $random = rand(0, 1);
    return $this->json([
      'Message' => $generadorDeMensajes->getMensaje($random) . ' las direcciones.',
      'data' => $data,
      'total' => $total,
      'lastPage' => $lastPage
    ]);
  }

  #[Route('/{id}', name: 'app_direccion_read_one', methods: ['GET'])]
  public function readOne(EntityManagerInterface $entityManager, int $id, GeneradorDeMensajes $generadorDeMensajes): JsonResponse
  {
    $direccion = $entityManager->getRepository(Direccion::class)->find($id);

    if (!$direccion) {
      return $this->json(['error' => 'No se encontro la direccion.'], 404);
    }
    $random = rand(0, 1);
    return $this->json([
      'Message' => $generadorDeMensajes->getMensaje($random) . ' la direccion',
      'id' => $direccion->getId(),
      'usuario_id' => $direccion->getUsuario()->getId(),
      'departamento' => $direccion->getDepartamento(),
      'municipio' => $direccion->getMunicipio(),
      'direccion' => $direccion->getDireccion()
    ]);
  }

  #[Route('/{id}', name: 'app_direccion_edit', methods: ['PUT'])]
  public function update(EntityManagerInterface $entityManager, int $id, Request $request, GeneradorDeMensajes $generadorDeMensajes): JsonResponse
  {
    // Busca la direccion por id
    $direccion = $entityManager->getRepository(Direccion::class)->find($id);

    // Si no lo encuentra responde con un error 404
    if (!$direccion) {
      return $this->json(['error' => 'No se encontro la direccion con id: ' . $id], 404);
    }

    // Obtiene los valores del body de la request
    $usuario_id = $request->request->get('usuario_id');
    $departamento = $request->request->get('departamento');
    $municipio = $request->request->get('municipio');
    $direcion = $request->request->get('direccion');

    // Si no envia uno responde con un error 422
    if ($usuario_id == null || $departamento == null || $municipio == null || $direccion == null) {
      return $this->json(['error' => 'Se debe enviar el usuario_id, departamento, municipio y direccion de la direccion.'], 422);
    }

    // Se actualizan los datos a la entidad
    $direccion->setUsuario($usuario_id);
    $direccion->setDepartamento($departamento);
    $direccion->setMunicipio($municipio);
    $direccion->setDireccion($direcion);

    $data = ['id' => $direccion->getId(), 'usuario_id' => $direccion->getUsario()->getId(), 'departamento' => $direccion->getDepartamento(), 'municipio' => $direccion->getMunicipio(), 'direccion' => $direccion->getDireccion()];

    // Se aplican los cambios de la entidad en la bd
    $entityManager->flush();
    $random = rand(0, 1);
    return $this->json([
      'Message' => $generadorDeMensajes->getMensaje($random) . ' la direccion.',
      'data' => $data
    ]);
  }

  #[Route('/{id}', name: 'app_direccion_delete', methods: ['DELETE'])]
  public function delete(EntityManagerInterface $entityManager, int $id, Request $request, GeneradorDeMensajes $generadorDeMensajes): JsonResponse
  {

    // Busca la direccion por id
    $direccion = $entityManager->getRepository(Direccion::class)->find($id);

    // Si no lo encuentra responde con un error 404
    if (!$direccion) {
      return $this->json(['error' => 'No se encontro la direccion con id: ' . $id], 404);
    }

    // Remueve la entidad
    $entityManager->remove($direccion);

    $data = ['id' => $direccion->getId(), 'usuario_id' => $direccion->getUsario()->getId(), 'departamento' => $direccion->getDepartamento(), 'municipio' => $direccion->getMunicipio(), 'direccion' => $direccion->getDireccion()];

    // Se aplican los cambios de la entidad en la bd
    $entityManager->flush();
    $random = rand(0,1);
    return $this->json([
      'Message' => $generadorDeMensajes->getMensaje($random) . ' de la direccion.',
      'data' => $data
    ]);
  }
}
