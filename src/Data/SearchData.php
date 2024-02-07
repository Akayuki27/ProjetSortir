<?php

namespace App\Data;

use App\Entity\Campus;
use DateTime;
use phpDocumentor\Reflection\Types\Boolean;

class SearchData
{
    public ?string $q;
    /**
     * @var DateTime| null
     */
    public ?DateTime $min;
    /**
     * @var DateTime| null
     */
    public ?DateTime $max;

    public bool $organisateur = false;
    public bool $inscrit = false;

    public bool $nonInscrit = false;

    public bool $passe = false;


    public Campus $campus;

}