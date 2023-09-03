<?php

use Efortmeyer\Polar\Api\Model;

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
