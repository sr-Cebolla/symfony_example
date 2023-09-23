<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/usuario', name: 'usuarios')]
class UsuarioController extends AbstractController
{
  #[Route('/{id}', name: 'getUsuario', methods: ['get'])]
  public function getUsuario(int $id): JsonResponse
  {
    // Este codigo simula una busqueda
    $usuario = ["id"=>$id, "nombre"=>"Juan", "edad"=>30];

    return $this->json($usuario);
  }
}
