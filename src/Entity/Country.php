<?php

namespace App\Entity;

use App\Repository\CountryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CountryRepository::class)]
class Country
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $code = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $fullname = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $region = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $subregion = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $area = null;

    #[ORM\Column(nullable: true)]
    private ?int $population = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $flag = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $alpha2code = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $alpha3code = null;

    #[ORM\Column(nullable: true)]
    private ?int $numericcode = null;

    /**
     * @var Collection<int, Language>
     */
    #[ORM\ManyToMany(targetEntity: Language::class, inversedBy: 'countries')]
    private Collection $languages;

    /**
     * @var Collection<int, Currency>
     */
    #[ORM\ManyToMany(targetEntity: Currency::class, inversedBy: 'countries')]
    private Collection $currencies;

    public function __construct()
    {
        $this->languages = new ArrayCollection();
        $this->currencies = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getFullname(): ?string
    {
        return $this->fullname;
    }

    public function setFullname(?string $fullname): static
    {
        $this->fullname = $fullname;

        return $this;
    }

    public function getRegion(): ?string
    {
        return $this->region;
    }

    public function setRegion(?string $region): static
    {
        $this->region = $region;

        return $this;
    }

    public function getSubregion(): ?string
    {
        return $this->subregion;
    }

    public function setSubregion(?string $subregion): static
    {
        $this->subregion = $subregion;

        return $this;
    }

    public function getArea(): ?string
    {
        return $this->area;
    }

    public function setArea(?string $area): static
    {
        $this->area = $area;

        return $this;
    }

    public function getPopulation(): ?int
    {
        return $this->population;
    }

    public function setPopulation(?int $population): static
    {
        $this->population = $population;

        return $this;
    }

    public function getFlag(): ?string
    {
        return $this->flag;
    }

    public function setFlag(?string $flag): static
    {
        $this->flag = $flag;

        return $this;
    }

    public function getAlpha2code(): ?string
    {
        return $this->alpha2code;
    }

    public function setAlpha2code(string $alpha2code): static
    {
        $this->alpha2code = $alpha2code;

        return $this;
    }

    public function getAlpha3code(): ?string
    {
        return $this->alpha3code;
    }

    public function setAlpha3code(?string $alpha3code): static
    {
        $this->alpha3code = $alpha3code;

        return $this;
    }

    public function getNumericcode(): ?int
    {
        return $this->numericcode;
    }

    public function setNumericcode(?int $numericcode): static
    {
        $this->numericcode = $numericcode;

        return $this;
    }

    /**
     * @return Collection<int, Language>
     */
    public function getLanguages(): Collection
    {
        return $this->languages;
    }

    public function addLanguage(Language $language): static
    {
        if (!$this->languages->contains($language)) {
            $this->languages->add($language);
        }

        return $this;
    }

    public function removeLanguage(Language $language): static
    {
        $this->languages->removeElement($language);

        return $this;
    }

    /**
     * @return Collection<int, Currency>
     */
    public function getCurrencies(): Collection
    {
        return $this->currencies;
    }

    public function addCurrency(Currency $currency): static
    {
        if (!$this->currencies->contains($currency)) {
            $this->currencies->add($currency);
        }

        return $this;
    }

    public function removeCurrency(Currency $currency): static
    {
        $this->currencies->removeElement($currency);

        return $this;
    }
}
