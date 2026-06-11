<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EventRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Event
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 255)]
    private ?string $title = null;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    private ?string $description = null;

    #[ORM\Column(type: 'datetime')]
    #[Assert\NotNull]
    #[Assert\GreaterThan('today')]
    private ?\DateTimeInterface $eventDate = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $endDate = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $location = null;

    #[ORM\Column(length: 80)]
    #[Assert\NotBlank]
    #[Assert\Choice(['Republique du Congo', 'Gabon', 'Cameroun', 'Senegal', "Cote d'Ivoire"])]
    private ?string $country = 'Republique du Congo';

    #[ORM\Column(length: 80)]
    #[Assert\NotBlank]
    #[Assert\Choice([
        'Brazzaville', 'Dolisie', 'Pointe-Noire', 'Nkayi',
        'Libreville', 'Port-Gentil', 'Franceville', 'Oyem',
        'Douala', 'Yaounde', 'Bafoussam', 'Garoua',
        'Dakar', 'Saly', 'Saint-Louis', 'Thies',
        'Abidjan', 'Yamoussoukro', 'Bouake', 'San-Pedro'
    ])]
    private ?string $city = null;

    #[ORM\Column(length: 120)]
    #[Assert\NotBlank]
    private ?string $district = null;

    #[ORM\Column(length: 40)]
    #[Assert\NotBlank]
    #[Assert\Choice(['Appartement', 'Maison', 'Studio', 'Villa', 'Bureau', 'Terrain'])]
    private ?string $propertyType = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank]
    #[Assert\Choice(['Location', 'Vente'])]
    private ?string $offerType = 'Location';

    #[ORM\Column]
    #[Assert\Positive]
    private ?int $monthlyRent = null;

    #[ORM\Column]
    #[Assert\Positive]
    private ?int $surfaceM2 = null;

    #[ORM\Column]
    #[Assert\PositiveOrZero]
    private ?int $bedrooms = 0;

    #[ORM\Column]
    #[Assert\PositiveOrZero]
    private ?int $bathrooms = 0;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imageUrl = null;

    #[ORM\Column]
    #[Assert\Positive]
    private ?int $maxParticipants = null;

    #[ORM\ManyToOne(inversedBy: 'organizedEvents')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $organizer = null;

    #[ORM\Column]
    private bool $isPublished = false;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\OneToMany(mappedBy: 'event', targetEntity: Registration::class, cascade: ['remove'])]
    private Collection $registrations;

    public function __construct()
    {
        $this->registrations = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }

    public function getTitle(): ?string { return $this->title; }
    public function setTitle(string $title): static { $this->title = $title; return $this; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(string $description): static { $this->description = $description; return $this; }

    public function getEventDate(): ?\DateTimeInterface { return $this->eventDate; }
    public function setEventDate(\DateTimeInterface $eventDate): static { $this->eventDate = $eventDate; return $this; }

    public function getEndDate(): ?\DateTimeInterface { return $this->endDate; }
    public function setEndDate(?\DateTimeInterface $endDate): static { $this->endDate = $endDate; return $this; }

    public function getLocation(): ?string { return $this->location; }
    public function setLocation(string $location): static { $this->location = $location; return $this; }

    public function getCountry(): ?string { return $this->country; }
    public function setCountry(string $country): static { $this->country = $country; return $this; }

    public function getCity(): ?string { return $this->city; }
    public function setCity(string $city): static { $this->city = $city; return $this; }

    public function getDistrict(): ?string { return $this->district; }
    public function setDistrict(string $district): static { $this->district = $district; return $this; }

    public function getPropertyType(): ?string { return $this->propertyType; }
    public function setPropertyType(string $propertyType): static { $this->propertyType = $propertyType; return $this; }

    public function getOfferType(): ?string { return $this->offerType; }
    public function setOfferType(string $offerType): static { $this->offerType = $offerType; return $this; }

    public function getMonthlyRent(): ?int { return $this->monthlyRent; }
    public function setMonthlyRent(int $monthlyRent): static { $this->monthlyRent = $monthlyRent; return $this; }

    public function getSurfaceM2(): ?int { return $this->surfaceM2; }
    public function setSurfaceM2(int $surfaceM2): static { $this->surfaceM2 = $surfaceM2; return $this; }

    public function getBedrooms(): ?int { return $this->bedrooms; }
    public function setBedrooms(int $bedrooms): static { $this->bedrooms = $bedrooms; return $this; }

    public function getBathrooms(): ?int { return $this->bathrooms; }
    public function setBathrooms(int $bathrooms): static { $this->bathrooms = $bathrooms; return $this; }

    public function getImageUrl(): ?string { return $this->imageUrl; }
    public function setImageUrl(?string $imageUrl): static { $this->imageUrl = $imageUrl; return $this; }

    public function getMaxParticipants(): ?int { return $this->maxParticipants; }
    public function setMaxParticipants(int $maxParticipants): static { $this->maxParticipants = $maxParticipants; return $this; }

    public function getOrganizer(): ?User { return $this->organizer; }
    public function setOrganizer(?User $organizer): static { $this->organizer = $organizer; return $this; }

    public function isPublished(): bool { return $this->isPublished; }
    public function setIsPublished(bool $isPublished): static { $this->isPublished = $isPublished; return $this; }

    public function getCreatedAt(): ?\DateTimeImmutable { return $this->createdAt; }
    public function setCreatedAt(\DateTimeImmutable $createdAt): static { $this->createdAt = $createdAt; return $this; }

    public function getRegistrations(): Collection { return $this->registrations; }

    public function getConfirmedParticipantsCount(): int
    {
        return $this->registrations->filter(fn($r) => $r->getStatus() === 'confirmed')->count();
    }

    public function getRemainingPlaces(): int
    {
        return max(0, $this->maxParticipants - $this->getConfirmedParticipantsCount());
    }

    public function isFull(): bool
    {
        return $this->getConfirmedParticipantsCount() >= $this->maxParticipants;
    }
}
