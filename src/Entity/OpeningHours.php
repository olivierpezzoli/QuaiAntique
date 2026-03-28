<?php

namespace App\Entity;

use App\Repository\OpeningHoursRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OpeningHoursRepository::class)]
class OpeningHours
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: "string", enumType: Weekday::class)]
    private Weekday $day;

    #[ORM\Column]
    private ?bool $isDayClosed = null;

    #[ORM\Column]
    private ?bool $isLunchClosed = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $lunchStart = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $lunchend = null;

    #[ORM\Column(nullable: true)]
    private ?int $lunchMaxPlaces = null;

    #[ORM\Column]
    private ?bool $isEveningClosed = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $eveningStart = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $eveningEnd = null;

    #[ORM\Column(nullable: true)]
    private ?int $eveningMaxPlaces = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDay(): Weekday
    {
        return $this->day;
    }

    public function setDay(Weekday $day)
    {
        $this->day = $day;

        return $this;
    }

    public function isDayClosed(): ?bool
    {
        return $this->isDayClosed;
    }

    public function setIsDayClosed(bool $isDayClosed): static
    {
        $this->isDayClosed = $isDayClosed;

        return $this;
    }

    public function isLunchClosed(): ?bool
    {
        return $this->isLunchClosed;
    }

    public function setIsLunchClosed(bool $isLunchClosed): static
    {
        $this->isLunchClosed = $isLunchClosed;

        return $this;
    }

    public function getLunchStart(): ?\DateTimeInterface
    {
        return $this->lunchStart;
    }

    public function setLunchStart(?\DateTimeInterface $lunchStart): static
    {
        $this->lunchStart = $lunchStart;

        return $this;
    }

    public function getLunchend(): ?\DateTimeInterface
    {
        return $this->lunchend;
    }

    public function setLunchend(?\DateTimeInterface $lunchend): static
    {
        $this->lunchend = $lunchend;

        return $this;
    }

    public function getLunchMaxPlaces(): ?int
    {
        return $this->lunchMaxPlaces;
    }

    public function setLunchMaxPlaces(?int $lunchMaxPlaces): static
    {
        $this->lunchMaxPlaces = $lunchMaxPlaces;

        return $this;
    }

    public function isEveningClosed(): ?bool
    {
        return $this->isEveningClosed;
    }

    public function setIsEveningClosed(bool $isEveningClosed): static
    {
        $this->isEveningClosed = $isEveningClosed;

        return $this;
    }

    public function getEveningStart(): ?\DateTimeInterface
    {
        return $this->eveningStart;
    }

    public function setEveningStart(?\DateTimeInterface $eveningStart): static
    {
        $this->eveningStart = $eveningStart;

        return $this;
    }

    public function getEveningEnd(): ?\DateTimeInterface
    {
        return $this->eveningEnd;
    }

    public function setEveningEnd(?\DateTimeInterface $eveningEnd): static
    {
        $this->eveningEnd = $eveningEnd;

        return $this;
    }

    public function getEveningMaxPlaces(): ?int
    {
        return $this->eveningMaxPlaces;
    }

    public function setEveningMaxPlaces(?int $eveningMaxPlaces): static
    {
        $this->eveningMaxPlaces = $eveningMaxPlaces;

        return $this;
    }

    public function __construct() {
        $this->day = Weekday::Monday;
    }
}
