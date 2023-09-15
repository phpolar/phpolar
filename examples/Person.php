<?php

use Phpolar\Phpolar\Model\AbstractModel;
use Phpolar\Phpolar\Model\Column;
use Phpolar\Phpolar\Model\Hidden;
use Phpolar\Phpolar\Model\Label;
use Phpolar\Phpolar\Validation\MaxLength;
use Phpolar\Phpolar\Validation\Required;

class Person extends AbstractModel
{
    #[Required]
    #[MaxLength(20)]
    public string $firstName;

    #[Required]
    #[MaxLength(20)]
    public string $lastName;

    #[Column("Residential Address")]
    #[Label("Residential Address")]
    #[MaxLength(200)]
    public string $address1;

    #[Column("Business Address")]
    #[Label("Business Address")]
    #[MaxLength(200)]
    public $address2;

    #[DateFormat("Y-m-d")]
    public DateTimeImmutable $dateOfBirth;

    #[Hidden]
    #[DateFormat("Y-m-d h:i:s a")]
    public DateTimeImmutable $enteredOn;

    #[DateFormat("Y-m-d h:i:s a")]
    public DateTimeImmutable $modifiedOn;
}
