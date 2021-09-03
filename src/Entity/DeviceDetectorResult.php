<?php

namespace App\Entity;

use App\Repository\DeviceDetectorResultRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DeviceDetectorResultRepository::class)
 * @ORM\Table(indexes={
 *     @ORM\Index(name="idx_device_detector_result_bench_id", columns={"bench_id"})
 * })
 */
class DeviceDetectorResult
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $bench_id;

    /**
     * @ORM\Column(type="float")
     */
    private $time;

    /**
     * @ORM\Column(type="integer")
     */
    private $memory;

    /**
     * @ORM\Column(type="integer")
     */
    private $score;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $client_name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $client_version;

    /**
     * @ORM\Column(type="integer")
     */
    private $parser_id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $engine_name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $engine_version;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $os_name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $os_version;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $data_json;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $device_type;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $brand_name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $model_name;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $is_bot;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $bot_name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $client_type;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBenchId(): ?int
    {
        return $this->bench_id;
    }

    public function setBenchId(int $bench_id): self
    {
        $this->bench_id = $bench_id;

        return $this;
    }

    public function getTime(): ?float
    {
        return $this->time;
    }

    public function setTime(float $time): self
    {
        $this->time = $time;

        return $this;
    }

    public function getMemory(): ?int
    {
        return $this->memory;
    }

    public function setMemory(int $memory): self
    {
        $this->memory = $memory;

        return $this;
    }

    public function getScore(): ?int
    {
        return $this->score;
    }

    public function setScore(int $score): self
    {
        $this->score = $score;

        return $this;
    }

    public function getClientName(): ?string
    {
        return $this->client_name;
    }

    public function setClientName(?string $client_name): self
    {
        $this->client_name = $client_name;

        return $this;
    }

    public function getClientVersion(): ?string
    {
        return $this->client_version;
    }

    public function setClientVersion(?string $client_version): self
    {
        $this->client_version = $client_version;

        return $this;
    }

    public function getParserId(): ?int
    {
        return $this->parser_id;
    }

    public function setParserId(int $parser_id): self
    {
        $this->parser_id = $parser_id;

        return $this;
    }

    public function getEngineName(): ?string
    {
        return $this->engine_name;
    }

    public function setEngineName(?string $engine_name): self
    {
        $this->engine_name = $engine_name;

        return $this;
    }

    public function getEngineVersion(): ?string
    {
        return $this->engine_version;
    }

    public function setEngineVersion(?string $engine_version): self
    {
        $this->engine_version = $engine_version;

        return $this;
    }

    public function getOsName(): ?string
    {
        return $this->os_name;
    }

    public function setOsName(?string $os_name): self
    {
        $this->os_name = $os_name;

        return $this;
    }

    public function getOsVersion(): ?string
    {
        return $this->os_version;
    }

    public function setOsVersion(?string $os_version): self
    {
        $this->os_version = $os_version;

        return $this;
    }

    public function getDataJson(): ?string
    {
        return $this->data_json;
    }

    public function setDataJson(?string $data_json): self
    {
        $this->data_json = $data_json;

        return $this;
    }

    public function getDeviceType(): ?string
    {
        return $this->device_type;
    }

    public function setDeviceType(?string $device_type): self
    {
        $this->device_type = $device_type;

        return $this;
    }

    public function getBrandName(): ?string
    {
        return $this->brand_name;
    }

    public function setBrandName(?string $brand_name): self
    {
        $this->brand_name = $brand_name;

        return $this;
    }

    public function getModelName(): ?string
    {
        return $this->model_name;
    }

    public function setModelName(?string $model_name): self
    {
        $this->model_name = $model_name;

        return $this;
    }

    public function getIsBot(): ?bool
    {
        return $this->is_bot;
    }

    public function setIsBot(?bool $is_bot): self
    {
        $this->is_bot = $is_bot;

        return $this;
    }

    public function getBotName(): ?string
    {
        return $this->bot_name;
    }

    public function setBotName(?string $bot_name): self
    {
        $this->bot_name = $bot_name;

        return $this;
    }

    public function getClientType(): ?string
    {
        return $this->client_type;
    }

    public function setClientType(?string $client_type): self
    {
        $this->client_type = $client_type;

        return $this;
    }
}
