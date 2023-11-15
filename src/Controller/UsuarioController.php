<?php

namespace App\Controller;

use App\Entity\Usuario;
use App\Entity\Direccion;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/usuario', name: 'usuarios')]
class UsuarioController extends AbstractController
{
  #[Route('', name: 'app_usuario_create', methods: ['POST'])]
  public function create(EntityManagerInterface $entityManager, Request $request): JsonResponse
  {
    $usuario = new Usuario();
    if ($request->request->get('edad') < 0) {
      return $this->json(['message' => 'no se puede ingresar un usuario con edad menor a 0']);
    }
    $usuario->setNombre($request->request->get('nombre'));
    $usuario->setEdad($request->request->get('edad'));
    // Se avisa a Doctrine que queremos guardar un nuevo registro pero no se ejecutan las consultas
    $entityManager->persist($usuario);

    // Se ejecutan las consultas SQL para guardar el nuevo registro
    $entityManager->flush();

    return $this->json([
      'message' => 'Se guardo el nuevo usuario con id ' . $usuario->getId()
    ]);
  }

  #[Route('', name: 'app_usuario_read_all', methods: ['GET'])]
  public function readAll(EntityManagerInterface $entityManager): JsonResponse
  {    
    $usuarios = $entityManager->getRepository(Usuario::class)->findAll();
    $direcciones = $entityManager->getRepository(Direccion::class)->findAll();
    $data = [];
    $dato = [];
    foreach ($usuarios as $usuario) {
      $data[] = [
        'id' => $usuario->getId(),
        'nombre' => $usuario->getNombre(),
        'edad' => $usuario->getEdad()
      ];
    }
    foreach ($direcciones as $direccion) {
      $dato[] = [
        'id' => $direccion->getId(),
        'usuario_id' => $direccion->getUsuario()->getId(),
        'departamento' => $direccion->getDepartamento(),
        'municipio' => $direccion->getMunicipio(),
        'direccion' => $direccion->getDireccion()
      ];
    }
    return $this->json([$data, $dato]);
  }

  #[Route('/{id}', name: 'app_usuario_read_one', methods: ['GET'])]
  public function readOne(EntityManagerInterface $entityManager, int $id): JsonResponse
  {
    $usuario = $entityManager->getRepository(Usuario::class)->find($id);
    $direccion = $entityManager->getRepository(Direccion::class)->find($usuario->getId());
    if (!$usuario) {
      return $this->json(['error' => 'No se encontro el usuario.'], 404);
    }

    $direcciones = $usuario->getDirecciones();

    $direccionData = [];

    foreach ($direcciones as $direccion) {
      $direccionData[] = [
        'id' => $direccion->getId(),
        'departamento' => $direccion->getDepartamento(),
        'municipio' => $direccion->getMunicipio(),
        'direccion' => $direccion->getDireccion(),
        'usuario_id' => $direccion->getUsuario()->getId()
      ];
    }

    if (!$direccionData) {
      return $this->json([
        'id' => $usuario->getId(),
        'nombre' => $usuario->getNombre(),
        'edad' => $usuario->getEdad(),
      ]);
    } else {
      return $this->json([
        'id' => $usuario->getId(),
        'nombre' => $usuario->getNombre(),
        'edad' => $usuario->getEdad(),
        'direcciones' => $direccionData,
      ]);
    }
  }

  #[Route('/{id}', name: 'app_usuario_edit', methods: ['PUT'])]
  public function update(EntityManagerInterface $entityManager, int $id, Request $request): JsonResponse
  {

    // Busca el usuario por id
    $usuario = $entityManager->getRepository(Usuario::class)->find($id);
    if ($request->request->get('edad') < 0) {
      return $this->json(['message' => 'no se puede modificar la edad de un usuario con edad menor a 0']);
    }
    // Si no lo encuentra responde con un error 404
    if (!$usuario) {
      return $this->json(['error' => 'No se encontro el usuario con id: ' . $id], 404);
    }

    // Obtiene los valores del body de la request
    $nombre = $request->request->get('nombre');
    $edad = $request->request->get('edad');

    // Si no envia uno responde con un error 422
    if ($nombre == null || $edad == null) {
      return $this->json(['error' => 'Se debe enviar el nombre y edad del usuario.'], 422);
    }

    // Se actualizan los datos a la entidad
    $usuario->setNombre($nombre);
    $usuario->setEdad($edad);

    $data = ['id' => $usuario->getId(), 'nombre' => $usuario->getNombre(), 'edad' => $usuario->getEdad()];

    // Se aplican los cambios de la entidad en la bd
    $entityManager->flush();

    return $this->json(['message' => 'Se actualizaron los datos del usuario.', 'data' => $data]);
  }

  #[Route('/{id}', name: 'app_usuario_delete', methods: ['DELETE'])]
  public function delete(EntityManagerInterface $entityManager, int $id, Request $request): JsonResponse
  {

    // Busca el usuario por id
    $usuario = $entityManager->getRepository(Usuario::class)->find($id);

    // Si no lo encuentra responde con un error 404
    if (!$usuario) {
      return $this->json(['error' => 'No se encontro el usuario con id: ' . $id], 404);
    }

    // Remueve la entidad
    $entityManager->remove($usuario);

    $data = ['id' => $usuario->getId(), 'nombre' => $usuario->getNombre(), 'edad' => $usuario->getEdad()];

    // Se aplican los cambios de la entidad en la bd
    $entityManager->flush();

    return $this->json(['message' => 'Se elimino el usuario.', 'data' => $data]);
  }
}
