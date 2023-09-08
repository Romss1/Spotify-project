<?php

namespace App\Common\Entity;

use App\Common\Repository\UserRepository;
use App\Worker\Entity\Track;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(length: 255, unique: true)]
    private string $spotifyClientId;

    #[ORM\Column(length: 255)]
    private string $refreshToken;

    #[ORM\Column(length: 255)]
    private string $scope;

    #[ORM\Column(type: 'datetime', options: ['default' => '2000-01-01'])]
    private \DateTime $lastCallToSpotifyApi;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Track::class)]
    private Collection $tracks;

    public function getId(): int
    {
        return $this->id;
    }

    public function getSpotifyClientId(): string
    {
        return $this->spotifyClientId;
    }

    public function setSpotifyClientId(string $spotifyClientId): void
    {
        $this->spotifyClientId = $spotifyClientId;
    }

    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }

    public function setRefreshToken(string $refreshToken): static
    {
        $this->refreshToken = $refreshToken;

        return $this;
    }

    public function getScope(): string
    {
        return $this->scope;
    }

    public function setScope(string $scope): static
    {
        $this->scope = $scope;

        return $this;
    }

    public function getLastCallToSpotifyApi(): \DateTime
    {
        return $this->lastCallToSpotifyApi;
    }

    public function setLastCallToSpotifyApi(\DateTime $lastCallToSpotifyApi): void
    {
        $this->lastCallToSpotifyApi = $lastCallToSpotifyApi;
    }

    public function getTracks(): Collection
    {
        return $this->tracks;
    }

    public function setTracks(Collection $tracks): void
    {
        $this->tracks = $tracks;
    }
}
