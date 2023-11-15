<?php

namespace App\Service;

class GeneradorDeMensajes
{
  private const mensajes = [
    'Encontramos estos datos de',
    'Hemos encontrado',     
    'Se elimino con exito',
    'Borramos definitivamente',
    'Enhorabuena has creado con exito',
    'Felicidades has agregado',    
    'Se cambiaron con exitos los datos de',
    'Modificaste exitosamente los datos de'
  ];

  public function getMensaje(int $idMensaje): string
  {
    return $this::mensajes[$idMensaje];
  }
}
