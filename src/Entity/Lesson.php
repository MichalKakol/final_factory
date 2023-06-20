<?php

namespace App\Entity;

use App\Repository\LessonRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity(repositoryClass: LessonRepository::class)]
class Lesson
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'lessons')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Training $training = null;

    #[ORM\ManyToOne(inversedBy: 'instructors')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Person $instructor = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $times = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dates = null;

    #[ORM\Column(length: 255)]
    private ?string $location = null;

    #[ORM\Column]
    private ?int $max_people = null;

    #[ORM\OneToMany(mappedBy: 'lesson', targetEntity: Registration::class)]
    private Collection $registrations;

    public function __construct()
    {
        $this->registrations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTraining(): ?Training
    {
        return $this->training;
    }

    public function setTraining(?Training $training): void
    {
        $this->training = $training;
    }

    public function getInstructor(): ?Person
    {
        return $this->instructor;
    }

    public function setInstructor(?Person $instructor): void
    {
        $this->instructor = $instructor;
    }

    public function getTimes(): ?\DateTimeInterface
    {
        return $this->times;
    }

    public function setTimes(\DateTimeInterface $times): void
    {
        $this->times = $times;
    }

    public function getDates(): ?\DateTimeInterface
    {
        return $this->dates;
    }

    public function setDates(\DateTimeInterface $dates): void
    {
        $this->dates = $dates;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): void
    {
        $this->location = $location;
    }

    public function getMaxPeople(): ?int
    {
        return $this->max_people;
    }

    public function setMaxPeople(int $max_people): void
    {
        $this->max_people = $max_people;
    }

    /**
     * @return Collection<int, Registration>
     */
    public function getRegistrations(): Collection
    {
        return $this->registrations;
    }

    public function addRegistration(Registration $registration): void
    {
        if (!$this->registrations->contains($registration)) {
            $this->registrations->add($registration);
            $registration->setLesson($this);
        }
    }

    public function removeRegistration(Registration $registration): void
    {
        if ($this->registrations->removeElement($registration)) {
            // set the owning side to null (unless already changed)
            if ($registration->getLesson() === $this) {
                $registration->setLesson(null);
            }
        }
    }

    public function __toString(): string
    {
        return $this->training ? (string) $this->training->getId() : '';
    }
}
