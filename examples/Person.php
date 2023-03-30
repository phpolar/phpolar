<?php

use Phpolar\Phpolar\Model\AbstractModel;
use Phpolar\Phpolar\Model\Column;
use Phpolar\Phpolar\Model\Hidden;
use Phpolar\Phpolar\Model\Label;
use Phpolar\Phpolar\Validation\MaxLength;
use Phpolar\Phpolar\Validation\Required;

class Person extends Model
{
    /**
     * @var string
     * @MaxLength(20)
     */
    public $firstName;

    /**
     * @var string
     * @MaxLength(20)
     */
    public $lastName;

    /**
     * @var string
     * @Column("Residential Address")
     * @Label("Residential Address")
     * @MaxLength(200)
     */
    public $address1;

    /**
     * @var string
     * @Column("Business Address")
     * @Label("Business Address")
     * @MaxLength(200)
     */
    public $address2;

    /**
     * @var DateTimeImmutable
     * @DateFormat(Y-m-d)
     */
    public $dateOfBirth;

    /**
     * @var DateTimeImmutable
     * @DateFormat("Y-m-d h:i:s a")
     */
    public $enteredOn;

    /**
     * @var DateTimeImmutable
     * @DateFormat("Y-m-d h:i:s a")
     */
    public $modifiedOn;
}
