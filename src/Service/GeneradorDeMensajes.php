<?php
namespace App\Service;

class GeneradorDeMensajes {
  private const mensajes = [
    'Buenos dias',
    'Buenas tardes',
    'Buenas noches',
  ];

  public function getMensaje(int $idMensaje): string
  {
    return $this::mensajes[$idMensaje];
  }
}