<?php

declare(strict_types=1);

namespace App\Presentation\Request\WorkEntry;

use App\Presentation\Request\BaseRequest;
use Symfony\Component\Validator\Constraints as Assert;

final class UpdateWorkEntryRequest extends BaseRequest
{
    #[Assert\DateTime]
    public ?string $startDate = null;

    #[Assert\DateTime]
    public ?string $endDate = null;
}
