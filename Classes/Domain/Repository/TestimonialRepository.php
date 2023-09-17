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
class TestimonialRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{

    /**
     * @var array
     */
    protected $defaultOrderings = ['sorting' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING];

    public function findWithStorageId($arguments): array {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_nstestimonial_domain_model_testimonial');
        $statement = $queryBuilder
        ->select('*')
        ->from('tx_nstestimonial_domain_model_testimonial')
        ->where(
            $queryBuilder->expr()->eq('pid', $queryBuilder->createNamedParameter($arguments))
        )
        ->execute();
        $result = $statement->fetch();
        \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($result, __FILE__.' Line '.__LINE__);die;

    }

    public function checkApiData()
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_nstestimonial_domain_model_apidata');
        $queryBuilder
            ->select('*')
            ->from('tx_nstestimonial_domain_model_apidata');
        $query = $queryBuilder->execute();
        return $query->fetch();
    }
    public function insertNewData($data)
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_nstestimonial_domain_model_apidata');
        $row = $queryBuilder
            ->insert('tx_nstestimonial_domain_model_apidata')
            ->values($data);

        $query = $queryBuilder->execute();
        return $query;
    }
    public function curlInitCall($url)
    {
        $curlSession = curl_init();
        curl_setopt($curlSession, CURLOPT_URL, $url);
        curl_setopt($curlSession, CURLOPT_BINARYTRANSFER, true);
        curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($curlSession);
        curl_close($curlSession);

        return $data;
    }
    public function deleteOldApiData()
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_nstestimonial_domain_model_apidata');
        $queryBuilder
            ->delete('tx_nstestimonial_domain_model_apidata')
            ->where(
                $queryBuilder->expr()->comparison('last_update', '<', 'DATE_SUB(NOW() , INTERVAL 1 DAY)')
            )
            ->execute();
    }
}
