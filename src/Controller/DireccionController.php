<?php

namespace App\Controller;

use App\Entity\Direccion;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/direccion', name: 'direccion')]
class DireccionController extends AbstractController
{
    #[Route('', name: 'app_direccion_create', methods:['POST'])]
    public function create(EntityManagerInterface $entityManager, Request $request): JsonResponse
    {
        $direccion = new Direccion();
        $direccion->setUsuario($request->request->get('usuario_id'));
        $direccion->setDepartamento($request->request->get('departamento'));
        $direccion->setMunicipio($request->request->get('municipio'));
        $direccion->setDireccion($request->request->get('direccion'));

        $entityManager->persist($direccion);

        $entityManager->flush();

        return $this->json([ 'message'=>'Se guardo la nueva direccion con id ' . $direccion->getId() ]);
    }
    
    #[Route('',name: 'app_direccion_read_all', methods:['GET'])]
    public function readAll(EntityManagerInterface $entityManager): JsonResponse{
        $direcciones = $entityManager->getRepository(Direccion::class)->findAll();

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

        return $this->json($data);
    }

#[Route('/{id}', name: 'app_direccion_read_one', methods: ['GET'])]
  public function readOne(EntityManagerInterface $entityManager, int $id): JsonResponse
  {
    $direccion = $entityManager->getRepository(Direccion::class)->find($id);

    if(!$direccion){
      return $this->json(['error'=>'No se encontro la direccion.'], 404);
    } 

    return $this->json([
      'id' => $direccion->getId(), 
      'usuario_id' => $direccion->getUsuario()->getId(),
      'departamento' => $direccion->getDepartamento(), 
      'municipio' => $direccion->getMunicipio(),
      'direccion' => $direccion->getDireccion()
    ]);  
  }
  
  #[Route('/{id}', name: 'app_direccion_edit', methods: ['PUT'])]
  public function update(EntityManagerInterface $entityManager, int $id, Request $request): JsonResponse
  {
    // Busca la direccion por id
    $direccion = $entityManager->getRepository(Direccion::class)->find($id);

    // Si no lo encuentra responde con un error 404
    if (!$direccion) {
      return $this->json(['error'=>'No se encontro la direccion con id: '.$id], 404);
    }
    
    // Obtiene los valores del body de la request
    $usuario_id = $request->request->get('usuario_id');
    $departamento = $request->request->get('departamento');
    $municipio = $request->request->get('municipio');
    $direcion = $request->request->get('direccion');

    // Si no envia uno responde con un error 422
    if ($usuario_id == null || $departamento == null || $municipio == null || $direccion == null){
      return $this->json(['error'=>'Se debe enviar el usuario_id, departamento, municipio y direccion de la direccion.'], 422);
    }

    // Se actualizan los datos a la entidad
    $direccion->setUsuario($usuario_id);
    $direccion->setDepartamento($departamento);
    $direccion->setMunicipio($municipio);
    $direccion->setDireccion($direcion);

    $data=['id' => $direccion->getId(),'usuario_id' => $direccion->getUsario()->getId(),'departamento' => $direccion->getDepartamento(), 'municipio' => $direccion->getMunicipio(), 'direccion'=>$direccion->getDireccion()];

    // Se aplican los cambios de la entidad en la bd
    $entityManager->flush();

    return $this->json(['message'=>'Se actualizaron los datos de la direccion.', 'data' => $data]);
  }

  #[Route('/{id}', name: 'app_direccion_delete', methods: ['DELETE'])]
  public function delete(EntityManagerInterface $entityManager, int $id, Request $request): JsonResponse
  {

    // Busca la direccion por id
    $direccion = $entityManager->getRepository(Direccion::class)->find($id);

    // Si no lo encuentra responde con un error 404
    if (!$direccion) {
      return $this->json(['error'=>'No se encontro la direccion con id: '.$id], 404);
    }

    // Remueve la entidad
    $entityManager->remove($direccion);

    $data=['id' => $direccion->getId(),'usuario_id' => $direccion->getUsario()->getId(),'departamento' => $direccion->getDepartamento(), 'municipio' => $direccion->getMunicipio(), 'direccion'=>$direccion->getDireccion()];

    // Se aplican los cambios de la entidad en la bd
    $entityManager->flush();

    return $this->json(['message'=>'Se elimino la direccion.', 'data' => $data]);
  }

}
