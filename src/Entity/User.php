<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="users")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", options={"unsigned":true})
     */
    private $id;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = ['ROLE_USER'];

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email = '';

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $pass;

    /**
     * @ORM\Column(type="string", length=16, nullable=true)
     */
    private $ref_code;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $birthday;

    /**
     * @ORM\Column(type="json")
     */
    private $meta = [];

    /**     
     * @ORM\Column(type="decimal", precision=10, scale=2, options={"default":0})
     */
    private $rewardSum = 0;

    /**
     * @ORM\Column(type="boolean", options={"default":0})
     */
    private $fundraiser = 0;

    /**
     * @ORM\Column(type="boolean", options={"default":1})
     */
    private $confirmed = 0;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $deletedAt;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="referrals", fetch="LAZY")
     * @ORM\JoinColumn(name="referrer_id", referencedColumnName="id")
     */
    private $referrer;

    /**
     * @ORM\OneToMany(targetEntity="User", mappedBy="referrer", fetch="LAZY")
     */
    private $referrals;

    /**
     * @ORM\OneToMany(targetEntity="ReferralHistory", mappedBy="user", fetch="LAZY")
     */
    private $referral_history;

    /**
     * @ORM\OneToMany(targetEntity="ReferralHistory", mappedBy="donator", fetch="LAZY")
     */
    private $donate_history;

    /**
     * @ORM\OneToMany (targetEntity="ChildHistory", mappedBy="donator", fetch="LAZY")
     */
    private $child_history;

    /**
     * @ORM\OneToMany(targetEntity="Request", mappedBy="user", fetch="LAZY")
     */
    private $requests;

    /**
     * @ORM\OneToMany(targetEntity="RecurringPayment", mappedBy="user", fetch="LAZY")
     */
    private $recurring_payments;

    /**
     * User constructor.
     *
     * @throws \Exception
     */
    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getFirstName(): string
    {
        return $this->meta['firstName'] ?? '';
    }

    public function setFirstName(string $firstName): self
    {
        $this->meta['firstName'] = $firstName;

        return $this;
    }

    public function getMiddleName(): string
    {
        return $this->meta['middleName'] ?? '';
    }

    public function setMiddleName(string $middleName): self
    {
        $this->meta['middleName'] = $middleName;

        return $this;
    }

    public function getLastName(): string
    {
        return $this->meta['lastName'] ?? '';
    }

    public function setLastName(?string $lastName): self
    {
        if ($lastName !== null)            
            $this->meta['lastName'] = $lastName;

        return $this;
    }

    public function getBirthday(): ?\DateTime
    {
        return $this->birthday;
    }

    public function setBirthday(?\DateTime $birthday)
    {
        $this->birthday = $birthday;

        return $this;
    }

    public function getAge(): ?string
    {
        if (empty($this->getBirthday()))
            return '';

        $now = new \DateTime();
        $diff = $now - $this->getBirthday();
        $age = floor($diff / (365*60*60*24));  
        return $age;
    }

    public function getPhone(): string
    {
        return $this->meta['phone'] ?? '';
    }

    public function setPhone(string $phone): self
    {
        $this->meta['phone'] = $phone;

        return $this;
    }

    public function getResultHash(): string
    {
        return $this->meta['resultHash'] ?? '';
    }

    public function setResultHash(string $hash): self
    {
        $this->meta['resultHash'] = $hash;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->pass;
    }

    public function getPass(): ?string
    {
        return $this->pass;
    }

    public function setPass(?string $pass): self
    {
        $this->pass = $pass;

        return $this;
    }

    public function getRefCode(): ?string
    {
        return $this->ref_code;
    }

    public function setRefCode(?string $ref_code): self
    {
        $this->ref_code = $ref_code;

        return $this;
    }

    public function getMeta(): ?array
    {
        return $this->meta;
    }

    public function setMeta(array $meta): self
    {
        $this->meta = $meta;

        return $this;
    }

    public function getChildHistory(): ?string
    {
        return $this->child_history;
    }

    public function setChildHistory(?string $child_history): self
    {
        $this->child_history = $child_history;

        return $this;
    }

    /**
     * @return float
     */
    public function getRewardSum()
    {
        return $this->rewardSum;
    }

    /**
     * @param float $rewardSum
     *
     * @return User
     */
    public function setRewardSum(float $rewardSum): self
    {
        $this->rewardSum = $rewardSum;

        return $this;
    }

    /**
     * @return bool
     */
    public function getFundraiser()
    {
        return $this->fundraiser;
    }

    /**
     * @param bool $fundraiser
     * 
     * @return User
     */
    public function setFundraiser(bool $fundraiser): self
    {
        $this->fundraiser = $fundraiser;

        return $this;
    }

    /**
     * @return bool
     */
    public function getConfirmed()
    {
        return $this->confirmed;
    }

    /**
     * @param bool $confirmed
     * 
     * @return User
     */
    public function setConfirmed(bool $confirmed): self
    {
        $this->confirmed = $confirmed;

        return $this;
    }

    public function getDeletedAt(): ?\DateTimeInterface
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTimeInterface $deletedAt): self
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getReferrer(): ?User
    {
        return $this->referrer;
    }

    public function setReferrer($referrer): self
    {
        $this->referrer = $referrer;

        return $this;
    }

    /**
     * @return ReferralHistory[]
     */
    public function getReferralHistory()
    {
        return $this->referral_history;
    }

    /**
     * @param ReferralHistory[] $referral_history
     *
     * @return User
     */
    public function setReferralHistory(array $referral_history): self
    {
        $this->referral_history = $referral_history;

        return $this;
    }

    /**
     * @return ReferralHistory[]
     */
    public function getDonateHistory()
    {
        return $this->donate_history;
    }

    /**
     * @param ReferralHistory[] $donate_history
     *
     * @return User
     */
    public function setDonateHistory(array $donate_history): self
    {
        $this->donate_history = $donate_history;

        return $this;
    }

    /**
     * @return RecurringPayment[]
     */
    public function getRecurringPayments()
    {
        return $this->recurring_payments;
    }

    /**
     * @param RecurringPayment[] $recurring_payments
     *
     * @return User
     */
    public function setRecurringPayments(array $recurring_payments): self
    {
        $this->recurring_payments = $recurring_payments;

        return $this;
    }

    public function getReferrals()
    {
        return $this->referrals;
    }

    public function setReferrals(array $referrals): self
    {
        $this->referrals = $referrals;

        return $this;
    }

    public function getRequests()
    {
        return $this->requests;
    }

    public function setRequests(array $requests): self
    {
        $this->requests = $requests;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    
}