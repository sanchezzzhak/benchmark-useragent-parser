<?php

namespace App\Entity;

use App\Repository\BenchmarkResultRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=BenchmarkResultRepository::class)
 */
class BenchmarkResult
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=500, nullable=true)
     */
    private $user_agent;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $matomo_id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $whichbrowser_id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $mimmi20_id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $status = 0;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $last_updated_at;

    /**
     * @ORM\Column(type="integer")
     */
    private $source_parser_id;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserAgent(): ?string
    {
        return $this->user_agent;
    }

    public function setUserAgent(?string $user_agent): self
    {
        $this->user_agent = $user_agent;

        return $this;
    }

    public function getMatomoId(): ?int
    {
        return $this->matomo_id;
    }

    public function setMatomoId(?int $matomo_id): self
    {
        $this->matomo_id = $matomo_id;

        return $this;
    }

    public function getWhichbrowserId(): ?int
    {
        return $this->whichbrowser_id;
    }

    public function setWhichbrowserId(?int $whichbrowser_id): self
    {
        $this->whichbrowser_id = $whichbrowser_id;

        return $this;
    }

    public function getMimmi20Id(): ?int
    {
        return $this->mimmi20_id;
    }

    public function setMimmi20Id(?int $mimmi20_id): self
    {
        $this->mimmi20_id = $mimmi20_id;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(?int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getLastUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->last_updated_at;
    }

    public function setLastUpdatedAt(?\DateTimeImmutable $last_updated_at): self
    {
        $this->last_updated_at = $last_updated_at;

        return $this;
    }

    public function getSourceParserId(): ?int
    {
        return $this->source_parser_id;
    }

    public function setSourceParserId(int $source_parser_id): self
    {
        $this->source_parser_id = $source_parser_id;

        return $this;
    }
}
