<?php

namespace App\Controller;

use App\Entity\Usuario;
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

    $data = [];
  
    foreach ($usuarios as $usuario) {
        $data[] = [
            'id' => $usuario->getId(),
            'nombre' => $usuario->getNombre(),
            'edad' => $usuario->getEdad(),
        ];
    }
    
    return $this->json($data); 
  }

  #[Route('/{id}', name: 'app_usuario_read_one', methods: ['GET'])]
  public function readOne(EntityManagerInterface $entityManager, int $id): JsonResponse
  {
    $usuario = $entityManager->getRepository(Usuario::class)->find($id);

    if(!$usuario){
      return $this->json(['error'=>'No se encontro el usuario.'], 404);
    }

    return $this->json([
      'id' => $usuario->getId(), 
      'nombre' => $usuario->getNombre(), 
      'edad' => $usuario->getEdad()
    ]);  
  }
}
