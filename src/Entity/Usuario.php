<?php

namespace App\Entity;

use App\Repository\UsuarioRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UsuarioRepository::class)]
class Usuario
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $nombre = null;

    #[ORM\Column]
    private ?int $edad = null;

    #[ORM\OneToMany(mappedBy: 'usuario', targetEntity: Direccion::class, orphanRemoval: true)]
    private Collection $direcciones;

    public function __construct()
    {
        $this->direcciones = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): static
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getEdad(): ?int
    {
        return $this->edad;
    }

    public function setEdad(int $edad): static
    {
        $this->edad = $edad;

        return $this;
    }

    /**
     * @return Collection<int, Direccion>
     */
    public function getDirecciones(): Collection
    {
        return $this->direcciones;
    }

    public function addDireccione(Direccion $direccione): static
    {
        if (!$this->direcciones->contains($direccione)) {
            $this->direcciones->add($direccione);
            $direccione->setUsuario($this);
        }

        return $this;
    }

    public function removeDireccione(Direccion $direccione): static
    {
        if ($this->direcciones->removeElement($direccione)) {
            // set the owning side to null (unless already changed)
            if ($direccione->getUsuario() === $this) {
                $direccione->setUsuario(null);
            }
        }

        return $this;
    }
}
