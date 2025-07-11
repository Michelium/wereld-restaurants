<?php

namespace App\Entity\Trait;

use Doctrine\ORM\Mapping as ORM;

trait EntityLifecycleTrait {

    #[ORM\Column(type: 'datetime', nullable: true, options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    private bool $visible = true;

    public function getCreatedAt(): ?\DateTimeInterface {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $created_at): self {
        $this->createdAt = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updated_at): self {
        $this->updatedAt = $updated_at;

        return $this;
    }

    public function isVisible(): bool {
        return $this->visible;
    }

    public function setVisible(bool $visible): self {
        $this->visible = $visible;
        return $this;
    }

}
