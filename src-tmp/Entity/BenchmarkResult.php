<?php

namespace App\Entity;

use App\Repository\BenchmarkResultRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=BenchmarkResultRepository::class)
 * @ORM\Table(indexes={
 *     @ORM\Index(name="idx_benchmark_result_user_agent", columns={"user_agent"}),
 *     @ORM\Index(name="idx_benchmark_result_source_parser_id", columns={"source_parser_id"})
 * })
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
