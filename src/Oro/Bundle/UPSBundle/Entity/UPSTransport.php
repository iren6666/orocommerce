<?php

namespace Oro\Bundle\UPSBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\HttpFoundation\ParameterBag;

use Oro\Bundle\DataAuditBundle\Metadata\Annotation as Oro;
use Oro\Bundle\IntegrationBundle\Entity\Transport;

/**
 * @ORM\Entity
 * @Oro\Loggable()
 */
class UPSTransport extends Transport
{
    /**
     * @var string
     *
     * @ORM\Column(name="ups_base_url", type="string", length=255, nullable=false)
     * @Oro\Versioned()
     */
    protected $baseUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="ups_api_user", type="string", length=255, nullable=false)
     * @Oro\Versioned()
     */
    protected $apiUser;

    /**
     * @var string
     *
     * @ORM\Column(name="ups_api_password", type="string", length=255, nullable=false)
     * @Oro\Versioned()
     */
    protected $apiPassword;

    /**
     * @var string
     *
     * @ORM\Column(name="ups_api_key", type="string", length=255, nullable=false)
     */
    protected $apiKey;

    /**
     * @var string
     *
     * @ORM\Column(name="ups_shipping_account_number", type="string", length=100, nullable=false)
     */
    protected $shippingAccountNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="ups_shipping_account_name", type="string", length=255, nullable=false)
     */
    protected $shippingAccountName;

    /**
     * @var Collection|UPSShippingService[]
     *
     * @ORM\OneToMany(targetEntity="UPSShippingService", mappedBy="transport", cascade={"ALL"}, orphanRemoval=true)
     * @ORM\OrderBy({"description" = "ASC"})
     */
    protected $applicableShippingServices;

    /**
     * @var ParameterBag
     */
    protected $settings;
    
    public function __construct()
    {
        $this->applicableShippingServices = new ArrayCollection();
    }

    /**
     * @param string $baseUrl
     *
     * @return $this
     */
    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = $baseUrl;

        return $this;
    }

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * @param string $apiUser
     *
     * @return $this
     */
    public function setApiUser($apiUser)
    {
        $this->apiUser = $apiUser;

        return $this;
    }

    /**
     * @return string
     */
    public function getApiUser()
    {
        return $this->apiUser;
    }

    /**
     * @param string $apiPassword
     *
     * @return $this
     */
    public function setApiPassword($apiPassword)
    {
        $this->apiPassword = $apiPassword;

        return $this;
    }

    /**
     * @return string
     */
    public function getApiPassword()
    {
        return $this->apiPassword;
    }

    /**
     * @param string $apiKey
     *
     * @return $this
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;

        return $this;
    }

    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * @param string $shippingAccountNumber
     *
     * @return $this
     */
    public function setShippingAccountNumber($shippingAccountNumber)
    {
        $this->shippingAccountNumber = $shippingAccountNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getShippingAccountNumber()
    {
        return $this->shippingAccountNumber;
    }

    /**
     * @param string $shippingAccountName
     *
     * @return $this
     */
    public function setShippingAccountName($shippingAccountName)
    {
        $this->shippingAccountName = $shippingAccountName;

        return $this;
    }

    /**
     * @return string
     */
    public function getShippingAccountName()
    {
        return $this->shippingAccountName;
    }

    /**
     * @return Collection|UPSShippingService[]
     */
    public function getApplicableShippingServices()
    {
        return $this->applicableShippingServices;
    }

    /**
     * @param string $code
     * @return UPSShippingService|null
     */
    public function getApplicableShippingService($code)
    {
        $result = null;

        foreach ($this->applicableShippingServices as $service) {
            if ($service->getCode() === $code) {
                $result = $service;
                break;
            }
        }

        return $result;
    }

    /**
     * @param UPSShippingService $service
     * @return $this
     */
    public function addApplicableShippingService(UPSShippingService $service)
    {
        if ($existingService = $this->getApplicableShippingService($service->getCode())) {
            $existingService
                ->setDescription($service->getDescription());
        } else {
            $service->setTransport($this);
            $this->applicableShippingServices->add($service);
        }

        return $this;
    }

    /**
     * @param UPSShippingService $service
     * @return $this
     */
    public function removeApplicableShippingService(UPSShippingService $service)
    {
        if ($this->applicableShippingServices->contains($service)) {
            $this->applicableShippingServices->removeElement($service);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSettingsBag()
    {
        if (null === $this->settings) {
            $this->settings = new ParameterBag(
                [
                    'api_user' => $this->getApiUser(),
                    'api_password' => $this->getApiPassword(),
                    'api_key' => $this->getApiKey(),
                    'base_url' => $this->getBaseUrl(),
                    'shipping_account_name' => $this->getShippingAccountName(),
                    'shipping_account_number' => $this->getShippingAccountNumber(),
                    'applicable_shipping_services' => $this->getApplicableShippingServices()->toArray(),
                ]
            );
        }

        return $this->settings;
    }
}
