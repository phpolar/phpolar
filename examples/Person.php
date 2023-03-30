<?php

use Phpolar\Phpolar\Model\AbstractModel;
use Phpolar\Phpolar\Model\Column;
use Phpolar\Phpolar\Model\Hidden;
use Phpolar\Phpolar\Model\Label;
use Phpolar\Phpolar\Validation\MaxLength;
use Phpolar\Phpolar\Validation\Required;

class Person extends AbstractModel
{
    public string $title = "Person Form";

    public string $action = "somewhere";

    #[MaxLength(20)]
    public string $firstName;

    #[MaxLength(20)]
    public string $lastName;

    #[Column("Residential Address")]
    #[Label("Residential Address")]
    #[MaxLength(200)]
    public string $address1;

    #[Column("Business Address")]
    #[Label("Business Address")]
    #[MaxLength(200)]
    #[Required]
    public string $address2;

    public DateTimeImmutable $dateOfBirth;

    #[Hidden]
    public DateTimeImmutable $enteredOn;

    #[Hidden]
    public DateTimeImmutable $modifiedOn;
}
