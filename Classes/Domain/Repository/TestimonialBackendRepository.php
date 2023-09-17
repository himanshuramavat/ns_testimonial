<?php

declare(strict_types=1);

namespace NITSAN\NsTestimonial\Domain\Repository;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;


/**
 * This file is part of the "Testimonial Showcase" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2023 HImasnhu <himanshu.nitsan@gmail.com>, NITSAN
 */

/**
 * The repository for Testimonials
 */
class TestimonialBackendRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{

    /**
     * @var array
     */
    protected $defaultOrderings = ['sorting' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING];

}
