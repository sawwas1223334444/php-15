<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\OrderFileRepository;
use Vich\UploaderBundle\Mapping\Annotation\Uploadable;
use Vich\UploaderBundle\Mapping\Annotation\UploadableField;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: OrderFileRepository::class)]
#[Uploadable]
class OrderFile
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[UploadableField(
        mapping: 'order_file', 
        fileNameProperty: 'filename', 
        size: 'size', 
        originalName: 'originalName', 
        mimeType: 'mimeType'
    )]
    #[Assert\File(
        maxSize: '5M',
        mimeTypes: [
            'application/pdf',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'text/plain',
            'image/jpeg',
            'image/png'
        ],
        mimeTypesMessage: 'Пожалуйста, загрузите PDF, DOCX, TXT, JPG или PNG файл'
    )]
    private ?File $file = null;

    #[ORM\Column(length: 255)]
    private ?string $filename = null;

    #[ORM\Column(length: 255)]
    private ?string $originalName = null;

    #[ORM\Column(length: 100)]
    private ?string $mimeType = null;

    #[ORM\Column]
    private ?int $size = null;

    #[ORM\ManyToOne(inversedBy: 'files')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Order $orderRelation = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setFile(?File $file = null): void
    {
        $this->file = $file;

        if (null !== $file) {
            // Обновляем updatedAt для триггера VichUploader
            $this->updatedAt = new \DateTimeImmutable();
            
            // Получаем оригинальное имя файла, если это UploadedFile
            if ($file instanceof UploadedFile) {
                $this->originalName = $file->getClientOriginalName();
            } else {
                // Для File объектов используем имя файла
                $this->originalName = $file->getFilename();
            }
            
            // Получаем MIME тип и размер
            $this->mimeType = $file->getMimeType();
            $this->size = $file->getSize();
        }
    }

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): static
    {
        $this->filename = $filename;

        return $this;
    }

    public function getOriginalName(): ?string
    {
        return $this->originalName;
    }

    public function setOriginalName(string $originalName): static
    {
        $this->originalName = $originalName;

        return $this;
    }

    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    public function setMimeType(string $mimeType): static
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function setSize(int $size): static
    {
        $this->size = $size;

        return $this;
    }

    public function getOrderRelation(): ?Order
    {
        return $this->orderRelation;
    }

    public function setOrderRelation(?Order $orderRelation): static
    {
        $this->orderRelation = $orderRelation;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getFileUrl(): string
    {
        return '/uploads/orders/'.$this->filename;
    }

    public function isImage(): bool
    {
        return in_array($this->mimeType, ['image/jpeg', 'image/png']);
    }

    public function getIconClass(): string
    {
        return match($this->mimeType) {
            'application/pdf' => 'fas fa-file-pdf text-danger',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'fas fa-file-word text-primary',
            'text/plain' => 'fas fa-file-alt text-secondary',
            'image/jpeg', 'image/png' => 'fas fa-file-image text-success',
            default => 'fas fa-file'
        };
    }
}